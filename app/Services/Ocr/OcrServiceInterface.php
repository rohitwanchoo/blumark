<?php

namespace App\Services\Ocr;

interface OcrServiceInterface
{
    /**
     * Get the engine identifier.
     */
    public function getEngine(): string;

    /**
     * Check if the service is available/configured.
     */
    public function isAvailable(): bool;

    /**
     * Extract text from a PDF file.
     */
    public function extractText(string $pdfPath, array $options = []): OcrResult;

    /**
     * Extract text from an image file.
     */
    public function extractTextFromImage(string $imagePath, array $options = []): OcrResult;

    /**
     * Detect if specific patterns (like watermarks) are present in the OCR output.
     */
    public function detectPatterns(string $pdfPath, array $patterns): array;
}
