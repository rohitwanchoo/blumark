<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LenderDistributionItem extends Model
{
    protected $fillable = [
        'lender_distribution_id',
        'lender_id',
        'watermark_job_id',
        'lender_snapshot',
        'source_file_index',
        'status',
        'error_message',
        'sent_at',
        'sent_via',
    ];

    protected function casts(): array
    {
        return [
            'lender_snapshot' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(LenderDistribution::class, 'lender_distribution_id');
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(Lender::class);
    }

    public function watermarkJob(): BelongsTo
    {
        return $this->belongsTo(WatermarkJob::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsDone(): void
    {
        $this->update(['status' => 'done']);
        $this->distribution->incrementProcessed();
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
        $this->distribution->incrementFailed();
    }

    public function markAsSent(string $via): void
    {
        $this->update([
            'sent_at' => now(),
            'sent_via' => $via,
        ]);
    }

    public function getLenderCompanyName(): string
    {
        return $this->lender_snapshot['company_name'] ?? 'Unknown';
    }

    public function getLenderEmail(): ?string
    {
        return $this->lender_snapshot['email'] ?? null;
    }

    public function getLenderFullName(): string
    {
        return $this->lender_snapshot['full_name'] ?? $this->getLenderCompanyName();
    }

    public function getSourceFile(): ?array
    {
        $sourceFiles = $this->distribution->getSourceFilesArray();
        return $sourceFiles[$this->source_file_index] ?? null;
    }

    public function getSourceFilename(): string
    {
        $sourceFile = $this->getSourceFile();
        return $sourceFile['filename'] ?? 'Unknown';
    }

    public function canDownload(): bool
    {
        return $this->isDone() && $this->watermarkJob && $this->watermarkJob->isDone();
    }

    public function canSend(): bool
    {
        return $this->canDownload() && $this->getLenderEmail();
    }
}
