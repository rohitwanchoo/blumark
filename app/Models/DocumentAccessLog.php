<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'watermark_job_id',
        'fingerprint_id',
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'referrer',
        'recipient_email',
        'geo_country',
        'geo_city',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // Action constants
    public const ACTION_DOWNLOAD = 'download';
    public const ACTION_VIEW = 'view';
    public const ACTION_VERIFY = 'verify';
    public const ACTION_SHARE = 'share';
    public const ACTION_PREVIEW = 'preview';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (!$log->created_at) {
                $log->created_at = now();
            }
        });
    }

    public function watermarkJob(): BelongsTo
    {
        return $this->belongsTo(WatermarkJob::class);
    }

    public function fingerprint(): BelongsTo
    {
        return $this->belongsTo(DocumentFingerprint::class, 'fingerprint_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabel(): string
    {
        return match($this->action) {
            self::ACTION_DOWNLOAD => 'Downloaded',
            self::ACTION_VIEW => 'Viewed',
            self::ACTION_VERIFY => 'Verified',
            self::ACTION_SHARE => 'Shared',
            self::ACTION_PREVIEW => 'Previewed',
            default => ucfirst($this->action),
        };
    }

    public function getActionBadgeClass(): string
    {
        return match($this->action) {
            self::ACTION_DOWNLOAD => 'bg-green-100 text-green-800',
            self::ACTION_VIEW, self::ACTION_PREVIEW => 'bg-blue-100 text-blue-800',
            self::ACTION_VERIFY => 'bg-purple-100 text-purple-800',
            self::ACTION_SHARE => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getLocation(): ?string
    {
        if ($this->geo_city && $this->geo_country) {
            return "{$this->geo_city}, {$this->geo_country}";
        }
        return $this->geo_country;
    }

    public function scopeForJob($query, int $jobId)
    {
        return $query->where('watermark_job_id', $jobId);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeDownloads($query)
    {
        return $query->where('action', self::ACTION_DOWNLOAD);
    }
}
