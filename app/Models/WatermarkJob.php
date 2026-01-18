<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class WatermarkJob extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'batch_job_id',
        'original_filename',
        'original_path',
        'output_path',
        'watermark_image_path',
        'status',
        'error_message',
        'settings',
        'page_count',
        'file_size',
        'processed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'processed_at' => 'datetime',
            'file_size' => 'integer',
            'page_count' => 'integer',
        ];
    }

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';
    public const STATUS_FAILED = 'failed';

    /**
     * Get the user that owns the watermark job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the batch job this job belongs to.
     */
    public function batchJob(): BelongsTo
    {
        return $this->belongsTo(BatchJob::class);
    }

    /**
     * Get the shared links for this job.
     */
    public function sharedLinks(): HasMany
    {
        return $this->hasMany(SharedLink::class);
    }

    /**
     * Get the document fingerprints for this job.
     */
    public function fingerprints(): HasMany
    {
        return $this->hasMany(DocumentFingerprint::class);
    }

    /**
     * Get the access logs for this job.
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class);
    }

    /**
     * Check if the job is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the job is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if the job is done.
     */
    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    /**
     * Check if the job has failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark the job as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    /**
     * Mark the job as done.
     */
    public function markAsDone(string $outputPath, ?int $pageCount = null): void
    {
        $this->update([
            'status' => self::STATUS_DONE,
            'output_path' => $outputPath,
            'page_count' => $pageCount,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark the job as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'processed_at' => now(),
        ]);
    }

    /**
     * Get the output filename.
     */
    public function getOutputFilename(): string
    {
        $info = pathinfo($this->original_filename);
        return ($info['filename'] ?? 'document') . '-watermarked.pdf';
    }

    /**
     * Check if the output file exists.
     */
    public function outputExists(): bool
    {
        return $this->output_path && Storage::exists($this->output_path);
    }

    /**
     * Get the full path to the original file.
     */
    public function getOriginalFullPath(): string
    {
        return Storage::path($this->original_path);
    }

    /**
     * Get the full path to the output file.
     */
    public function getOutputFullPath(): ?string
    {
        return $this->output_path ? Storage::path($this->output_path) : null;
    }

    /**
     * Get the full path to the watermark image.
     */
    public function getWatermarkImageFullPath(): ?string
    {
        return $this->watermark_image_path ? Storage::path($this->watermark_image_path) : null;
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedFileSize(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor] ?? 'B');
    }

    /**
     * Get the watermark type from settings.
     */
    public function getWatermarkType(): string
    {
        return $this->settings['type'] ?? 'text';
    }

    /**
     * Get the position mode from settings.
     */
    public function getPositionMode(): string
    {
        return $this->settings['position'] ?? 'diagonal';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_DONE => 'bg-green-100 text-green-800',
            self::STATUS_FAILED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Delete associated files.
     */
    public function deleteFiles(): void
    {
        if ($this->original_path && Storage::exists($this->original_path)) {
            Storage::delete($this->original_path);
        }

        if ($this->output_path && Storage::exists($this->output_path)) {
            Storage::delete($this->output_path);
        }

        if ($this->watermark_image_path && Storage::exists($this->watermark_image_path)) {
            Storage::delete($this->watermark_image_path);
        }
    }

    /**
     * Scope a query to only include jobs for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include jobs older than retention period.
     */
    public function scopeOlderThan($query, int $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }
}
