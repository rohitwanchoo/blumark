<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'iso',
        'lender',
        'settings',
        'status',
        'total_files',
        'processed_files',
        'failed_files',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'completed_at' => 'datetime',
        'total_files' => 'integer',
        'processed_files' => 'integer',
        'failed_files' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function watermarkJobs(): HasMany
    {
        return $this->hasMany(WatermarkJob::class);
    }

    /**
     * Check if batch is still processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if batch is complete.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get completion percentage.
     */
    public function getProgressPercentage(): int
    {
        if ($this->total_files === 0) {
            return 0;
        }

        return (int) round(($this->processed_files / $this->total_files) * 100);
    }

    /**
     * Mark a file as processed.
     */
    public function markFileProcessed(): void
    {
        $this->increment('processed_files');
        $this->checkCompletion();
    }

    /**
     * Mark a file as failed.
     */
    public function markFileFailed(string $error = null): void
    {
        $this->increment('failed_files');
        $this->increment('processed_files');

        if ($error) {
            $currentErrors = $this->error_message ? $this->error_message . "\n" : '';
            $this->update(['error_message' => $currentErrors . $error]);
        }

        $this->checkCompletion();
    }

    /**
     * Check if all files are processed and update status.
     */
    protected function checkCompletion(): void
    {
        if ($this->processed_files >= $this->total_files) {
            $status = $this->failed_files === $this->total_files ? 'failed' : 'completed';
            $this->update([
                'status' => $status,
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope to get user's batches.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
