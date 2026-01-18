<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'type',
        'severity',
        'status',
        'title',
        'description',
        'metadata',
        'ip_address',
        'watermark_job_id',
        'user_id',
        'acknowledged_by',
        'acknowledged_at',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // Alert types
    public const TYPE_MULTI_JOB_IP = 'multi_job_ip';
    public const TYPE_RAPID_DOWNLOAD = 'rapid_download';
    public const TYPE_HIGH_RISK_DOCUMENT = 'high_risk_document';
    public const TYPE_EXCESSIVE_DOWNLOADS = 'excessive_downloads';
    public const TYPE_UNUSUAL_GEO = 'unusual_geo';

    // Severity levels
    public const SEVERITY_CRITICAL = 'critical';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_LOW = 'low';

    // Status values
    public const STATUS_NEW = 'new';
    public const STATUS_ACKNOWLEDGED = 'acknowledged';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';

    public function watermarkJob(): BelongsTo
    {
        return $this->belongsTo(WatermarkJob::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function acknowledgedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get human-readable type label.
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_MULTI_JOB_IP => 'Multi-Job IP Access',
            self::TYPE_RAPID_DOWNLOAD => 'Rapid Downloads',
            self::TYPE_HIGH_RISK_DOCUMENT => 'High-Risk Document',
            self::TYPE_EXCESSIVE_DOWNLOADS => 'Excessive Downloads',
            self::TYPE_UNUSUAL_GEO => 'Unusual Location',
            default => ucwords(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Get severity badge CSS class.
     */
    public function getSeverityBadgeClass(): string
    {
        return match($this->severity) {
            self::SEVERITY_CRITICAL => 'bg-red-600 text-white',
            self::SEVERITY_HIGH => 'bg-orange-500 text-white',
            self::SEVERITY_MEDIUM => 'bg-yellow-500 text-white',
            self::SEVERITY_LOW => 'bg-blue-500 text-white',
            default => 'bg-gray-500 text-white',
        };
    }

    /**
     * Get status badge CSS class.
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_NEW => 'bg-red-100 text-red-800',
            self::STATUS_ACKNOWLEDGED => 'bg-yellow-100 text-yellow-800',
            self::STATUS_RESOLVED => 'bg-green-100 text-green-800',
            self::STATUS_DISMISSED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Check if the alert can be actioned.
     */
    public function isActionable(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_ACKNOWLEDGED]);
    }

    /**
     * Scope to get unresolved alerts.
     */
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_ACKNOWLEDGED]);
    }

    /**
     * Scope to filter by severity.
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for recent alerts.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
