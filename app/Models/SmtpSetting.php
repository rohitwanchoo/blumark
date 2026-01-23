<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class SmtpSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'provider',
        'provider_type',
        'provider_data',
        'oauth_tokens',
        'token_expires_at',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_email',
        'from_name',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'port' => 'integer',
    ];

    protected $hidden = [
        'password',
        'provider_data',
        'oauth_tokens',
    ];

    /**
     * Automatically encrypt password when setting
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    /**
     * Automatically decrypt password when getting
     */
    public function getPasswordAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Encrypt provider_data when setting
     */
    public function setProviderDataAttribute($value)
    {
        if ($value) {
            $json = is_array($value) ? json_encode($value) : $value;
            $this->attributes['provider_data'] = Crypt::encryptString($json);
        }
    }

    /**
     * Decrypt provider_data when getting
     */
    public function getProviderDataAttribute($value)
    {
        if ($value) {
            try {
                $decrypted = Crypt::decryptString($value);
                return json_decode($decrypted, true);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Encrypt oauth_tokens when setting
     */
    public function setOauthTokensAttribute($value)
    {
        if ($value) {
            $json = is_array($value) ? json_encode($value) : $value;
            $this->attributes['oauth_tokens'] = Crypt::encryptString($json);
        }
    }

    /**
     * Decrypt oauth_tokens when getting
     */
    public function getOauthTokensAttribute($value)
    {
        if ($value) {
            try {
                $decrypted = Crypt::decryptString($value);
                return json_decode($decrypted, true);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Check if provider is OAuth-based
     */
    public function isOAuthProvider(): bool
    {
        return $this->provider_type === 'oauth';
    }

    /**
     * Check if provider is API key-based
     */
    public function isApiKeyProvider(): bool
    {
        return $this->provider_type === 'api_key';
    }

    /**
     * Get the user that owns the SMTP setting
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark this setting as recently used
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get the active SMTP setting for a user
     */
    public static function getActiveForUser(int $userId): ?self
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Convert to mail config array
     */
    public function toMailConfig(): array
    {
        return [
            'transport' => 'smtp',
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption,
            'username' => $this->username,
            'password' => $this->password,
            'timeout' => null,
            'local_domain' => null,
        ];
    }

    /**
     * Test connection to SMTP server
     */
    public function testConnection(): array
    {
        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($this->host, $this->port);
            $transport->setUsername($this->username);
            $transport->setPassword($this->password);

            if ($this->encryption === 'tls') {
                $transport->setEncryption('tls');
            } elseif ($this->encryption === 'ssl') {
                $transport->setEncryption('ssl');
            }

            // Try to start the transport
            $transport->start();

            return [
                'success' => true,
                'message' => 'Connection successful!',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }
}
