<?php

namespace App\Services\Ocr;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class GoogleVisionOcrService implements OcrServiceInterface
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('watermark.ocr.google_vision_key');
    }

    public function getEngine(): string
    {
        return 'google_vision';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function extractText(string $pdfPath, array $options = []): OcrResult
    {
        $startTime = microtime(true);
        $tempDir = sys_get_temp_dir() . '/ocr_gv_' . uniqid();
        mkdir($tempDir, 0755, true);

        try {
            // Convert PDF to images
            $imagePrefix = $tempDir . '/page';
            $convertResult = Process::timeout(300)->run(
                "pdftoppm -png -r 150 " . escapeshellarg($pdfPath) . " " . escapeshellarg($imagePrefix)
            );

            if (!$convertResult->successful()) {
                throw new \Exception('Failed to convert PDF to images');
            }

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
            Log::error('Google Vision OCR failed', [
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
            array_map('unlink', glob($tempDir . '/*'));
            @rmdir($tempDir);
        }
    }

    public function extractTextFromImage(string $imagePath, array $options = []): OcrResult
    {
        $startTime = microtime(true);

        try {
            if (!$this->isAvailable()) {
                throw new \Exception('Google Vision API key not configured');
            }

            $imageContent = base64_encode(file_get_contents($imagePath));

            $response = Http::timeout(60)->post(
                "https://vision.googleapis.com/v1/images:annotate?key={$this->apiKey}",
                [
                    'requests' => [
                        [
                            'image' => [
                                'content' => $imageContent,
                            ],
                            'features' => [
                                [
                                    'type' => 'DOCUMENT_TEXT_DETECTION',
                                    'maxResults' => 1,
                                ],
                            ],
                        ],
                    ],
                ]
            );

            if (!$response->successful()) {
                throw new \Exception('API request failed: ' . $response->body());
            }

            $data = $response->json();
            $annotation = $data['responses'][0]['fullTextAnnotation'] ?? null;

            if (!$annotation) {
                return new OcrResult(
                    text: '',
                    confidence: 0,
                    processingTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                    pagesProcessed: 1
                );
            }

            // Calculate average confidence from pages
            $confidence = 0;
            $pageCount = count($annotation['pages'] ?? []);
            foreach ($annotation['pages'] ?? [] as $page) {
                foreach ($page['blocks'] ?? [] as $block) {
                    $confidence += $block['confidence'] ?? 0;
                }
            }
            $confidence = $pageCount > 0 ? $confidence / $pageCount : 0;

            return new OcrResult(
                text: $annotation['text'] ?? '',
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
}
