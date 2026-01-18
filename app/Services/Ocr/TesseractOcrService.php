<?php

namespace App\Services\Ocr;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TesseractOcrService implements OcrServiceInterface
{
    protected string $tesseractPath;
    protected string $language;

    public function __construct()
    {
        $this->tesseractPath = config('watermark.ocr.tesseract_path', '/usr/bin/tesseract');
        $this->language = config('watermark.ocr.tesseract_language', 'eng');
    }

    public function getEngine(): string
    {
        return 'tesseract';
    }

    public function isAvailable(): bool
    {
        if (!file_exists($this->tesseractPath)) {
            return false;
        }

        $result = Process::run($this->tesseractPath . ' --version');
        return $result->successful();
    }

    public function extractText(string $pdfPath, array $options = []): OcrResult
    {
        $startTime = microtime(true);
        $tempDir = sys_get_temp_dir() . '/ocr_' . uniqid();
        mkdir($tempDir, 0755, true);

        try {
            // Convert PDF to images using pdftoppm
            $imagePrefix = $tempDir . '/page';
            $convertResult = Process::timeout(300)->run(
                "pdftoppm -png -r 200 " . escapeshellarg($pdfPath) . " " . escapeshellarg($imagePrefix)
            );

            if (!$convertResult->successful()) {
                throw new \Exception('Failed to convert PDF to images: ' . $convertResult->errorOutput());
            }

            // Find all generated images
            $images = glob($tempDir . '/page-*.png');
            sort($images, SORT_NATURAL);

            if (empty($images)) {
                throw new \Exception('No images generated from PDF');
            }

            $allText = [];
            $pageResults = [];
            $totalConfidence = 0;

            foreach ($images as $index => $imagePath) {
                $pageNum = $index + 1;
                $pageResult = $this->extractTextFromImage($imagePath, $options);

                $allText[] = "=== Page {$pageNum} ===\n" . $pageResult->text;
                $pageResults[] = [
                    'page' => $pageNum,
                    'text' => $pageResult->text,
                    'confidence' => $pageResult->confidence,
                    'word_count' => $pageResult->getWordCount(),
                ];
                $totalConfidence += $pageResult->confidence;
            }

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);
            $avgConfidence = count($images) > 0 ? $totalConfidence / count($images) : 0;

            return new OcrResult(
                text: implode("\n\n", $allText),
                confidence: $avgConfidence,
                processingTimeMs: $processingTime,
                pagesProcessed: count($images),
                pageResults: $pageResults
            );
        } catch (\Exception $e) {
            Log::error('Tesseract OCR failed', [
                'pdf' => $pdfPath,
                'error' => $e->getMessage(),
            ]);

            return new OcrResult(
                text: '',
                confidence: 0,
                processingTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                pagesProcessed: 0,
                error: $e->getMessage()
            );
        } finally {
            // Cleanup temp files
            array_map('unlink', glob($tempDir . '/*'));
            @rmdir($tempDir);
        }
    }

    public function extractTextFromImage(string $imagePath, array $options = []): OcrResult
    {
        $startTime = microtime(true);

        try {
            $outputBase = sys_get_temp_dir() . '/tesseract_' . uniqid();
            $outputFile = $outputBase . '.txt';

            // Build tesseract command
            $cmd = sprintf(
                '%s %s %s -l %s',
                escapeshellarg($this->tesseractPath),
                escapeshellarg($imagePath),
                escapeshellarg($outputBase),
                escapeshellarg($this->language)
            );

            // Add additional options
            if (!empty($options['psm'])) {
                $cmd .= ' --psm ' . (int) $options['psm'];
            }

            // Get confidence data
            $cmd .= ' -c tessedit_create_tsv=1';

            $result = Process::timeout(120)->run($cmd);

            if (!$result->successful()) {
                throw new \Exception('Tesseract failed: ' . $result->errorOutput());
            }

            $text = file_exists($outputFile) ? file_get_contents($outputFile) : '';
            $confidence = $this->parseConfidence($outputBase . '.tsv');

            // Cleanup
            @unlink($outputFile);
            @unlink($outputBase . '.tsv');

            return new OcrResult(
                text: trim($text),
                confidence: $confidence,
                processingTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                pagesProcessed: 1
            );
        } catch (\Exception $e) {
            return new OcrResult(
                text: '',
                confidence: 0,
                processingTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                pagesProcessed: 0,
                error: $e->getMessage()
            );
        }
    }

    public function detectPatterns(string $pdfPath, array $patterns): array
    {
        $result = $this->extractText($pdfPath);

        if (!$result->isSuccessful()) {
            return [
                'success' => false,
                'error' => $result->error,
                'patterns_found' => [],
            ];
        }

        $found = $result->containsAnyPattern($patterns);

        return [
            'success' => true,
            'patterns_found' => $found,
            'all_patterns' => $patterns,
            'detection_rate' => count($patterns) > 0 ? count($found) / count($patterns) : 0,
            'text_length' => strlen($result->text),
            'confidence' => $result->confidence,
        ];
    }

    protected function parseConfidence(string $tsvPath): float
    {
        if (!file_exists($tsvPath)) {
            return 0;
        }

        $lines = file($tsvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $confidences = [];

        foreach ($lines as $line) {
            $parts = explode("\t", $line);
            // TSV format: level, page_num, block_num, par_num, line_num, word_num, left, top, width, height, conf, text
            if (count($parts) >= 11 && is_numeric($parts[10]) && (int) $parts[10] > 0) {
                $confidences[] = (float) $parts[10];
            }
        }

        return count($confidences) > 0 ? array_sum($confidences) / count($confidences) / 100 : 0;
    }
}
