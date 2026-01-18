<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DocumentFingerprint extends Model
{
    protected $fillable = [
        'watermark_job_id',
        'fingerprint_hash',
        'output_file_hash',
        'recipient_id',
        'recipient_email',
        'recipient_name',
        'unique_marker',
        'metadata_json',
        'verification_token',
        'verified_at',
        'last_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata_json' => 'encrypted:array',
            'verified_at' => 'datetime',
            'last_verified_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fingerprint) {
            if (empty($fingerprint->verification_token)) {
                $fingerprint->verification_token = Str::random(64);
            }
            if (empty($fingerprint->unique_marker)) {
                $fingerprint->unique_marker = self::generateUniqueMarker();
            }
        });
    }

    public static function generateUniqueMarker(): string
    {
        return Str::upper(Str::random(8)) . '-' .
               Str::upper(Str::random(4)) . '-' .
               Str::upper(Str::random(4)) . '-' .
               Str::upper(Str::random(12));
    }

    public function watermarkJob(): BelongsTo
    {
        return $this->belongsTo(WatermarkJob::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class, 'fingerprint_id');
    }

    public function verificationAttempts(): HasMany
    {
        return $this->hasMany(VerificationAttempt::class, 'fingerprint_id');
    }

    public function getDecryptedMetadata(): ?array
    {
        if (empty($this->metadata_json)) {
            return null;
        }

        try {
            $key = config('watermark.verification.encryption_key', config('app.key'));
            $decrypted = decrypt($this->metadata_json);
            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setEncryptedMetadata(array $data): void
    {
        $this->metadata_json = encrypt(json_encode($data));
    }

    public function markAsVerified(): void
    {
        $now = now();
        if (!$this->verified_at) {
            $this->verified_at = $now;
        }
        $this->last_verified_at = $now;
        $this->save();
    }

    public function getVerificationUrl(): string
    {
        return url('/verify/' . $this->verification_token);
    }

    public function isExpired(): bool
    {
        $expiryDays = config('watermark.verification.token_expiry_days', 365);
        return $this->created_at->addDays($expiryDays)->isPast();
    }

    public function scopeByToken($query, string $token)
    {
        return $query->where('verification_token', $token);
    }

    public function scopeByMarker($query, string $marker)
    {
        return $query->where('unique_marker', $marker);
    }

    public function scopeForRecipient($query, string $email)
    {
        return $query->where('recipient_email', $email);
    }
}
