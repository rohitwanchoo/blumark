<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /**
     * Action type constants
     */
    public const ACTION_USER_VIEW = 'user.view';
    public const ACTION_USER_UPDATE = 'user.update';
    public const ACTION_USER_DELETE = 'user.delete';
    public const ACTION_USER_CREDITS = 'user.credits';
    public const ACTION_USER_ROLE = 'user.role';
    public const ACTION_JOB_VIEW = 'job.view';
    public const ACTION_JOB_DELETE = 'job.delete';
    public const ACTION_IMPERSONATION_START = 'impersonation.start';
    public const ACTION_IMPERSONATION_STOP = 'impersonation.stop';

    /**
     * Get the admin who performed the action.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the action label for display.
     */
    public function getActionLabel(): string
    {
        return match($this->action) {
            self::ACTION_USER_VIEW => 'Viewed User',
            self::ACTION_USER_UPDATE => 'Updated User',
            self::ACTION_USER_DELETE => 'Deleted User',
            self::ACTION_USER_CREDITS => 'Modified Credits',
            self::ACTION_USER_ROLE => 'Changed Role',
            self::ACTION_JOB_VIEW => 'Viewed Job',
            self::ACTION_JOB_DELETE => 'Deleted Job',
            self::ACTION_IMPERSONATION_START => 'Started Impersonation',
            self::ACTION_IMPERSONATION_STOP => 'Stopped Impersonation',
            default => ucwords(str_replace(['.', '_'], ' ', $this->action)),
        };
    }

    /**
     * Get the action badge color class.
     */
    public function getActionBadgeClass(): string
    {
        return match($this->action) {
            self::ACTION_USER_DELETE, self::ACTION_JOB_DELETE => 'bg-red-100 text-red-800',
            self::ACTION_USER_UPDATE, self::ACTION_USER_CREDITS, self::ACTION_USER_ROLE => 'bg-blue-100 text-blue-800',
            self::ACTION_IMPERSONATION_START, self::ACTION_IMPERSONATION_STOP => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by admin.
     */
    public function scopeByAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
