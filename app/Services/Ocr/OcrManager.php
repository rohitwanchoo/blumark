<?php

namespace App\Services\Ocr;

use InvalidArgumentException;

class OcrManager
{
    protected array $engines = [];
    protected ?string $defaultEngine = null;

    public function __construct()
    {
        $this->defaultEngine = config('watermark.ocr.default_engine', 'tesseract');
        $this->registerDefaultEngines();
    }

    protected function registerDefaultEngines(): void
    {
        $this->engines['tesseract'] = fn() => new TesseractOcrService();
        $this->engines['google_vision'] = fn() => new GoogleVisionOcrService();
    }

    /**
     * Register a custom OCR engine.
     */
    public function register(string $name, callable $resolver): void
    {
        $this->engines[$name] = $resolver;
    }

    /**
     * Get an OCR service instance.
     */
    public function engine(?string $name = null): OcrServiceInterface
    {
        $name = $name ?? $this->defaultEngine;

        if (!isset($this->engines[$name])) {
            throw new InvalidArgumentException("OCR engine [{$name}] is not registered.");
        }

        return call_user_func($this->engines[$name]);
    }

    /**
     * Get the default engine.
     */
    public function getDefault(): OcrServiceInterface
    {
        return $this->engine($this->defaultEngine);
    }

    /**
     * Set the default engine.
     */
    public function setDefault(string $name): void
    {
        if (!isset($this->engines[$name])) {
            throw new InvalidArgumentException("OCR engine [{$name}] is not registered.");
        }
        $this->defaultEngine = $name;
    }

    /**
     * Get all available engines.
     */
    public function getAvailableEngines(): array
    {
        $available = [];

        foreach ($this->engines as $name => $resolver) {
            try {
                $engine = call_user_func($resolver);
                if ($engine->isAvailable()) {
                    $available[$name] = $engine->getEngine();
                }
            } catch (\Exception $e) {
                // Engine not available
            }
        }

        return $available;
    }

    /**
     * Extract text using the best available engine.
     */
    public function extractText(string $pdfPath, array $options = []): OcrResult
    {
        $engine = $options['engine'] ?? null;
        return $this->engine($engine)->extractText($pdfPath, $options);
    }

    /**
     * Detect patterns using the best available engine.
     */
    public function detectPatterns(string $pdfPath, array $patterns, ?string $engine = null): array
    {
        return $this->engine($engine)->detectPatterns($pdfPath, $patterns);
    }

    /**
     * Run OCR with multiple engines and compare results.
     */
    public function compareEngines(string $pdfPath, array $patterns = []): array
    {
        $results = [];

        foreach ($this->getAvailableEngines() as $name => $label) {
            try {
                $engine = $this->engine($name);
                $ocrResult = $engine->extractText($pdfPath);

                $results[$name] = [
                    'engine' => $label,
                    'available' => true,
                    'text' => $ocrResult->text,
                    'confidence' => $ocrResult->confidence,
                    'processing_time_ms' => $ocrResult->processingTimeMs,
                    'pages_processed' => $ocrResult->pagesProcessed,
                    'word_count' => $ocrResult->getWordCount(),
                    'error' => $ocrResult->error,
                ];

                if (!empty($patterns)) {
                    $results[$name]['patterns_found'] = $ocrResult->containsAnyPattern($patterns);
                }
            } catch (\Exception $e) {
                $results[$name] = [
                    'engine' => $name,
                    'available' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
