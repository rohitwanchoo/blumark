<?php

namespace App\Jobs;

use App\Mail\LenderDocumentMail;
use App\Models\EmailTemplate;
use App\Models\WatermarkJob;
use App\Services\CustomSmtpMailer;
use App\Services\DocumentFingerprintService;
use App\Services\PdfWatermarkService;
use Exception;
use Illuminate\Bus\Queueable;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessWatermarkPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WatermarkJob $watermarkJob
    ) {
        $this->timeout = config('watermark.processing.timeout', 300);
    }

    /**
     * Execute the job.
     */
    public function handle(PdfWatermarkService $service): void
    {
        // Set memory limit for large PDFs
        $memoryLimit = config('watermark.processing.memory_limit', '512M');
        ini_set('memory_limit', $memoryLimit);

        $this->watermarkJob->markAsProcessing();

        try {
            $inputPath = $this->watermarkJob->getOriginalFullPath();
            $outputPath = $this->generateOutputPath();
            $settings = $this->watermarkJob->settings;

            // Ensure output directory exists
            $outputDir = dirname($outputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Process based on watermark type
            $result = match ($settings['type'] ?? 'text') {
                'iso_lender' => $this->processIsoLenderWatermark($service, $inputPath, $outputPath, $settings),
                'image' => $this->processImageWatermark($service, $inputPath, $outputPath, $settings),
                default => $this->processTextWatermark($service, $inputPath, $outputPath, $settings),
            };

            // Convert full path to storage path for database
            $storagePath = $this->fullPathToStoragePath($outputPath);

            $this->watermarkJob->markAsDone($storagePath, $result['page_count'] ?? null);

            // Update lender distribution item status if this is part of a distribution
            $this->updateDistributionItemStatus(true);

            // Generate document fingerprint for fraud detection
            $this->generateFingerprint();

            Log::info('Watermark job completed', [
                'job_id' => $this->watermarkJob->id,
                'page_count' => $result['page_count'] ?? 'unknown',
            ]);

        } catch (Exception $e) {
            $this->handleFailure($e);
        }
    }

    /**
     * Process ISO/Lender watermark (9-position grid).
     * Uses text-preserving method to keep PDFs searchable.
     */
    protected function processIsoLenderWatermark(PdfWatermarkService $service, string $inputPath, string $outputPath, array $settings): array
    {
        // Use text-preserving watermark method to keep PDF searchable
        return $service->watermarkIsoLenderPreserveText($inputPath, $outputPath, $settings);
    }

    /**
     * Process text watermark.
     */
    protected function processTextWatermark(PdfWatermarkService $service, string $inputPath, string $outputPath, array $settings): array
    {
        return $service->watermarkText($inputPath, $outputPath, $settings);
    }

    /**
     * Process image watermark.
     */
    protected function processImageWatermark(PdfWatermarkService $service, string $inputPath, string $outputPath, array $settings): array
    {
        $imagePath = $this->watermarkJob->getWatermarkImageFullPath();

        if (!$imagePath || !file_exists($imagePath)) {
            throw new Exception('Watermark image not found.');
        }

        return $service->watermarkImage($inputPath, $outputPath, $imagePath, $settings);
    }

    /**
     * Generate the output file path.
     */
    protected function generateOutputPath(): string
    {
        $outputDir = config('watermark.paths.outputs', 'private/watermark/outputs');
        $filename = Str::uuid() . '.pdf';

        return Storage::path($outputDir . '/' . $filename);
    }

    /**
     * Convert full path to storage-relative path.
     */
    protected function fullPathToStoragePath(string $fullPath): string
    {
        $storagePath = Storage::path('');

        if (str_starts_with($fullPath, $storagePath)) {
            return substr($fullPath, strlen($storagePath));
        }

        return $fullPath;
    }

    /**
     * Generate document fingerprint for fraud detection.
     */
    protected function generateFingerprint(): void
    {
        if (!config('watermark.security.fingerprint_enabled', true)) {
            return;
        }

        try {
            $fingerprintService = app(DocumentFingerprintService::class);
            $fingerprint = $fingerprintService->generateFingerprint($this->watermarkJob);

            Log::info('Document fingerprint generated', [
                'job_id' => $this->watermarkJob->id,
                'fingerprint_id' => $fingerprint->id,
                'verification_token' => $fingerprint->verification_token,
            ]);
        } catch (Exception $e) {
            // Log but don't fail the job if fingerprinting fails
            Log::warning('Failed to generate document fingerprint', [
                'job_id' => $this->watermarkJob->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle job failure.
     */
    protected function handleFailure(Exception $e): void
    {
        $errorMessage = $e->getMessage();

        Log::error('Watermark job failed', [
            'job_id' => $this->watermarkJob->id,
            'error' => $errorMessage,
            'trace' => $e->getTraceAsString(),
        ]);

        $this->watermarkJob->markAsFailed($errorMessage);

        // Update lender distribution item status if this is part of a distribution
        $this->updateDistributionItemStatus(false, $errorMessage);
    }

    /**
     * Update lender distribution item status if this job is part of a distribution.
     */
    protected function updateDistributionItemStatus(bool $success, ?string $errorMessage = null): void
    {
        $item = $this->watermarkJob->lenderDistributionItem;

        if (!$item) {
            return;
        }

        if ($success) {
            $item->markAsDone();
            $this->sendEmailToLender($item);
        } else {
            $item->markAsFailed($errorMessage);
        }
    }

    /**
     * Send consolidated email to lender after ALL their files are watermarked.
     */
    protected function sendEmailToLender($item): void
    {
        // Skip if already sent
        if ($item->isSent()) {
            return;
        }

        $distribution = $item->distribution;

        // Get all items for the same lender in this distribution
        $lenderEmail = $item->getLenderEmail();
        $lenderItems = $distribution->items()
            ->where('lender_snapshot->email', $lenderEmail)
            ->get();

        // Check if ALL items for this lender are done (watermarking complete)
        $allDone = $lenderItems->every(fn($i) => $i->watermarkJob?->status === 'done');

        if (!$allDone) {
            Log::info('Waiting for all lender files to complete before sending email', [
                'item_id' => $item->id,
                'lender' => $item->getLenderCompanyName(),
                'done' => $lenderItems->filter(fn($i) => $i->watermarkJob?->status === 'done')->count(),
                'total' => $lenderItems->count(),
            ]);
            return;
        }

        // Check if any items for this lender are already sent (email already dispatched)
        $anySent = $lenderItems->contains(fn($i) => $i->isSent());
        if ($anySent) {
            return;
        }

        // Get only items that can be sent (have valid output files)
        $sendableItems = $lenderItems->filter(fn($i) => $i->canSend());

        if ($sendableItems->isEmpty()) {
            Log::info('Skipping auto-send: no sendable items for lender', [
                'lender' => $item->getLenderCompanyName(),
            ]);
            return;
        }

        try {
            $user = $distribution->user;

            // Get email template: distribution template > user default
            $template = $distribution->emailTemplate
                ?? EmailTemplate::getDefaultForUser($user->id);

            // Get SMTP settings to determine from address
            $fromEmail = null;
            $fromName = null;
            if ($distribution->smtp_setting_id) {
                $smtpSetting = \App\Models\SmtpSetting::where('id', $distribution->smtp_setting_id)
                    ->where('user_id', $user->id)
                    ->first();
                if ($smtpSetting) {
                    $fromEmail = $smtpSetting->from_email;
                    $fromName = $smtpSetting->from_name;
                }
            } else {
                $smtpSetting = \App\Models\SmtpSetting::getActiveForUser($user->id);
                if ($smtpSetting) {
                    $fromEmail = $smtpSetting->from_email;
                    $fromName = $smtpSetting->from_name;
                }
            }

            // Send ONE email with ALL attachments for this lender using custom SMTP if configured
            CustomSmtpMailer::sendWithCustomSmtp(
                $user->id,
                new LenderDocumentMail(
                    distribution: $distribution,
                    items: $sendableItems,
                    senderName: $user->getFullName(),
                    senderCompany: $user->company_name ?? $user->name,
                    attachPdf: true,
                    template: $template,
                    fromEmail: $fromEmail,
                    fromName: $fromName,
                ),
                $lenderEmail,
                $distribution->smtp_setting_id
            );

            // Mark ALL items for this lender as sent
            foreach ($sendableItems as $lenderItem) {
                $lenderItem->markAsSent('email_attachment');
            }

            Log::info('Auto-sent consolidated email to lender', [
                'lender' => $item->getLenderCompanyName(),
                'email' => $lenderEmail,
                'documents_count' => $sendableItems->count(),
                'item_ids' => $sendableItems->pluck('id')->toArray(),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to auto-send email to lender', [
                'lender' => $item->getLenderCompanyName(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure after all retries.
     */
    public function failed(?Throwable $exception): void
    {
        $errorMessage = $exception?->getMessage() ?? 'Unknown error occurred';

        Log::error('Watermark job permanently failed', [
            'job_id' => $this->watermarkJob->id,
            'error' => $errorMessage,
        ]);

        $this->watermarkJob->markAsFailed("Job failed after {$this->tries} attempts: {$errorMessage}");
    }
}
