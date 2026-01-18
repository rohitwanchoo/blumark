<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OcrTestResult extends Model
{
    protected $fillable = [
        'watermark_job_id',
        'tested_by',
        'ocr_engine',
        'extracted_text',
        'watermark_detected',
        'detected_patterns',
        'confidence_score',
        'processing_time_ms',
        'pages_processed',
        'test_config',
        'analysis_result',
    ];

    protected function casts(): array
    {
        return [
            'watermark_detected' => 'boolean',
            'detected_patterns' => 'array',
            'test_config' => 'array',
            'analysis_result' => 'array',
            'confidence_score' => 'float',
        ];
    }

    // Engine constants
    public const ENGINE_TESSERACT = 'tesseract';
    public const ENGINE_GOOGLE_VISION = 'google_vision';
    public const ENGINE_AWS_TEXTRACT = 'aws_textract';

    public function watermarkJob(): BelongsTo
    {
        return $this->belongsTo(WatermarkJob::class);
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by');
    }

    public function getEngineLabel(): string
    {
        return match($this->ocr_engine) {
            self::ENGINE_TESSERACT => 'Tesseract OCR',
            self::ENGINE_GOOGLE_VISION => 'Google Cloud Vision',
            self::ENGINE_AWS_TEXTRACT => 'AWS Textract',
            default => ucfirst($this->ocr_engine),
        };
    }

    public function getResultSummary(): string
    {
        if ($this->watermark_detected) {
            $count = count($this->detected_patterns ?? []);
            return "Watermark detected ({$count} patterns found)";
        }
        return "No watermark detected in OCR output";
    }

    public function getProcessingTimeFormatted(): string
    {
        if (!$this->processing_time_ms) {
            return 'N/A';
        }

        if ($this->processing_time_ms < 1000) {
            return $this->processing_time_ms . 'ms';
        }

        return round($this->processing_time_ms / 1000, 2) . 's';
    }

    public function getConfidencePercentage(): ?int
    {
        if ($this->confidence_score === null) {
            return null;
        }
        return (int) round($this->confidence_score * 100);
    }

    public function scopeForJob($query, int $jobId)
    {
        return $query->where('watermark_job_id', $jobId);
    }

    public function scopeEngine($query, string $engine)
    {
        return $query->where('ocr_engine', $engine);
    }

    public function scopeWithWatermarkDetected($query)
    {
        return $query->where('watermark_detected', true);
    }
}
