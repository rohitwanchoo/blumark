<?php

namespace App\Services\Ocr;

class OcrResult
{
    public function __construct(
        public readonly string $text,
        public readonly float $confidence,
        public readonly int $processingTimeMs,
        public readonly int $pagesProcessed,
        public readonly array $pageResults = [],
        public readonly ?string $error = null,
        public readonly ?string $engine = null
    ) {}

    public function isSuccessful(): bool
    {
        return $this->error === null;
    }

    public function containsPattern(string $pattern): bool
    {
        return preg_match('/' . preg_quote($pattern, '/') . '/i', $this->text) === 1;
    }

    public function containsAnyPattern(array $patterns): array
    {
        $found = [];
        foreach ($patterns as $pattern) {
            if ($this->containsPattern($pattern)) {
                $found[] = $pattern;
            }
        }
        return $found;
    }

    public function getWordCount(): int
    {
        return str_word_count($this->text);
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'confidence' => $this->confidence,
            'processing_time_ms' => $this->processingTimeMs,
            'pages_processed' => $this->pagesProcessed,
            'page_results' => $this->pageResults,
            'error' => $this->error,
            'word_count' => $this->getWordCount(),
            'engine' => $this->engine,
        ];
    }
}
