<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'fingerprint_id',
        'verification_token',
        'status',
        'verification_method',
        'request_ip',
        'request_data',
        'result_data',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'request_data' => 'array',
            'result_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // Status constants
    public const STATUS_VALID = 'valid';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_TAMPERED = 'tampered';
    public const STATUS_NOT_FOUND = 'not_found';

    // Method constants
    public const METHOD_TOKEN = 'token';
    public const METHOD_UPLOAD = 'upload';
    public const METHOD_QR = 'qr';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attempt) {
            if (!$attempt->created_at) {
                $attempt->created_at = now();
            }
        });
    }

    public function fingerprint(): BelongsTo
    {
        return $this->belongsTo(DocumentFingerprint::class, 'fingerprint_id');
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_VALID;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_VALID => 'Valid',
            self::STATUS_INVALID => 'Invalid',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_TAMPERED => 'Tampered',
            self::STATUS_NOT_FOUND => 'Not Found',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_VALID => 'bg-green-100 text-green-800',
            self::STATUS_INVALID, self::STATUS_NOT_FOUND => 'bg-red-100 text-red-800',
            self::STATUS_EXPIRED => 'bg-yellow-100 text-yellow-800',
            self::STATUS_TAMPERED => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getMethodLabel(): string
    {
        return match($this->verification_method) {
            self::METHOD_TOKEN => 'Token/URL',
            self::METHOD_UPLOAD => 'File Upload',
            self::METHOD_QR => 'QR Code',
            default => ucfirst($this->verification_method),
        };
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_VALID);
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', [
            self::STATUS_INVALID,
            self::STATUS_EXPIRED,
            self::STATUS_TAMPERED,
            self::STATUS_NOT_FOUND,
        ]);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
