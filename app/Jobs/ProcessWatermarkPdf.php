<?php

namespace App\Jobs;

use App\Models\WatermarkJob;
use App\Services\DocumentFingerprintService;
use App\Services\PdfWatermarkService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Illuminate\Bus\Queueable;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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

        $qrCodePath = null;

        try {
            $inputPath = $this->watermarkJob->getOriginalFullPath();
            $outputPath = $this->generateOutputPath();
            $settings = $this->watermarkJob->settings;

            // Ensure output directory exists
            $outputDir = dirname($outputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Generate QR code with verification URL before watermarking
            if (config('watermark.security.qr_watermark_enabled', true)) {
                $qrCodePath = $this->generateVerificationQrCode();
                if ($qrCodePath) {
                    $settings['qr_code_path'] = $qrCodePath;
                }
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
        } finally {
            // Cleanup QR code temp file
            if ($qrCodePath && file_exists($qrCodePath)) {
                @unlink($qrCodePath);
            }
        }
    }

    /**
     * Generate QR code with verification URL.
     */
    protected function generateVerificationQrCode(): ?string
    {
        try {
            // Generate verification URL using job ID
            $verificationUrl = url('/verify/job/' . $this->watermarkJob->id);

            $builder = new Builder(
                writer: new PngWriter(),
                data: $verificationUrl,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 400, // Large size for better scanning
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
            );

            $result = $builder->build();

            // Save to temp file
            $tempPath = sys_get_temp_dir() . '/qr_' . uniqid() . '.png';
            $result->saveToFile($tempPath);

            return $tempPath;
        } catch (Exception $e) {
            Log::warning('Failed to generate QR code', [
                'job_id' => $this->watermarkJob->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Process ISO/Lender watermark (9-position grid).
     */
    protected function processIsoLenderWatermark(PdfWatermarkService $service, string $inputPath, string $outputPath, array $settings): array
    {
        return $service->watermarkIsoLender($inputPath, $outputPath, $settings);
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
        } else {
            $item->markAsFailed($errorMessage);
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
