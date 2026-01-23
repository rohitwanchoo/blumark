<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class LenderDistribution extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'source_filename',
        'source_path',
        'source_files',
        'settings',
        'email_template_id',
        'smtp_setting_id',
        'status',
        'total_lenders',
        'processed_count',
        'failed_count',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'source_files' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function getSourceFilesArray(): array
    {
        // Return source_files if it exists, otherwise build from legacy single file
        if (!empty($this->source_files)) {
            return $this->source_files;
        }

        if (!empty($this->source_path)) {
            return [[
                'filename' => $this->source_filename,
                'path' => $this->source_path,
            ]];
        }

        return [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LenderDistributionItem::class);
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function smtpSetting(): BelongsTo
    {
        return $this->belongsTo(SmtpSetting::class);
    }

    public function getSourceFullPathAttribute(): ?string
    {
        if (empty($this->source_path)) {
            return null;
        }
        return Storage::path($this->source_path);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function incrementProcessed(): void
    {
        $this->increment('processed_count');
        $this->checkCompletion();
    }

    public function incrementFailed(): void
    {
        $this->increment('failed_count');
        $this->checkCompletion();
    }

    protected function checkCompletion(): void
    {
        $this->refresh();
        $totalProcessed = $this->processed_count + $this->failed_count;

        if ($totalProcessed >= $this->total_lenders) {
            if ($this->failed_count === $this->total_lenders) {
                $this->markAsFailed();
            } else {
                $this->markAsCompleted();
            }
        }
    }

    public function getProgressPercentage(): int
    {
        if ($this->total_lenders === 0) {
            return 0;
        }
        $totalProcessed = $this->processed_count + $this->failed_count;
        return (int) round(($totalProcessed / $this->total_lenders) * 100);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
