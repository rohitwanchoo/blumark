<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SharedLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'watermark_job_id',
        'token',
        'recipient_email',
        'recipient_name',
        'expires_at',
        'download_count',
        'max_downloads',
        'password_hash',
        'last_accessed_at',
        'last_accessed_ip',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'download_count' => 'integer',
        'max_downloads' => 'integer',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function watermarkJob(): BelongsTo
    {
        return $this->belongsTo(WatermarkJob::class);
    }

    /**
     * Generate a secure token for the link.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Check if the link has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the link is still valid.
     */
    public function isValid(): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->max_downloads && $this->download_count >= $this->max_downloads) {
            return false;
        }

        return true;
    }

    /**
     * Check if the link is password protected.
     */
    public function hasPassword(): bool
    {
        return !empty($this->password_hash);
    }

    /**
     * Verify the password.
     */
    public function verifyPassword(string $password): bool
    {
        if (!$this->hasPassword()) {
            return true;
        }

        return password_verify($password, $this->password_hash);
    }

    /**
     * Record an access attempt.
     */
    public function recordAccess(string $ip): void
    {
        $this->update([
            'download_count' => $this->download_count + 1,
            'last_accessed_at' => now(),
            'last_accessed_ip' => $ip,
        ]);
    }

    /**
     * Get the full shareable URL.
     */
    public function getUrl(): string
    {
        return route('share.download', ['token' => $this->token]);
    }

    /**
     * Scope to get only valid (non-expired, within download limit) links.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
            ->where(function ($q) {
                $q->whereNull('max_downloads')
                    ->orWhereColumn('download_count', '<', 'max_downloads');
            });
    }
}
