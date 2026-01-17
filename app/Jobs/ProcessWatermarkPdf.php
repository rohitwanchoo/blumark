<?php

namespace App\Jobs;

use App\Models\WatermarkJob;
use App\Services\PdfWatermarkService;
use Exception;
use Illuminate\Bus\Queueable;
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
    }

    /**
     * Handle a job failure after all retries.
     */
    public function failed(?Exception $exception): void
    {
        $errorMessage = $exception?->getMessage() ?? 'Unknown error occurred';

        Log::error('Watermark job permanently failed', [
            'job_id' => $this->watermarkJob->id,
            'error' => $errorMessage,
        ]);

        $this->watermarkJob->markAsFailed("Job failed after {$this->tries} attempts: {$errorMessage}");
    }
}
