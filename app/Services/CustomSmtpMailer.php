<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;

class CustomSmtpMailer
{
    /**
     * Send an email using custom SMTP settings if configured
     *
     * @param int $userId User ID
     * @param mixed $mailable The mailable to send
     * @param string $to Recipient email
     * @param int|null $smtpSettingId Optional specific SMTP setting ID to use
     */
    public static function sendWithCustomSmtp($userId, $mailable, $to, $smtpSettingId = null)
    {
        $smtpSetting = null;

        // If a specific SMTP setting ID is provided, use that
        if ($smtpSettingId) {
            $smtpSetting = SmtpSetting::where('id', $smtpSettingId)
                ->where('user_id', $userId)
                ->first();
        }

        // Fall back to active SMTP if no specific setting or setting not found
        if (!$smtpSetting) {
            $smtpSetting = SmtpSetting::getActiveForUser($userId);
        }

        \Log::info('CustomSmtpMailer sending email', [
            'user_id' => $userId,
            'to' => $to,
            'smtp_id' => $smtpSetting?->id,
            'smtp_name' => $smtpSetting?->name,
            'from_email' => $smtpSetting?->from_email,
        ]);

        if ($smtpSetting) {
            // Use custom SMTP
            static::sendWithSmtpSettings($smtpSetting, $mailable, $to);
            $smtpSetting->markAsUsed();
        } else {
            // Use default mailer (send immediately to maintain consistency)
            Mail::to($to)->sendNow($mailable);
        }
    }

    /**
     * Send email using specific SMTP settings
     */
    protected static function sendWithSmtpSettings(SmtpSetting $settings, $mailable, $to)
    {
        // For OAuth providers (like Gmail), we need to handle authentication differently
        if ($settings->isOAuthProvider() && $settings->oauth_tokens) {
            static::sendWithOAuth($settings, $mailable, $to);
            return;
        }

        // Configure transport for regular SMTP
        // TLS parameter: null = STARTTLS (port 587), true = SSL/TLS (port 465), false = no encryption
        $tls = null;
        if ($settings->encryption === 'ssl') {
            $tls = true; // Use SSL/TLS from the start (for port 465)
        } elseif ($settings->encryption === 'tls') {
            $tls = null; // Use STARTTLS (for port 587)
        } elseif (!$settings->encryption) {
            $tls = false; // No encryption
        }

        $transport = new EsmtpTransport($settings->host, $settings->port, $tls);
        $transport->setUsername($settings->username);
        $transport->setPassword($settings->password);

        // Create a custom mailer instance
        $mailer = new Mailer(
            'smtp-custom',
            app('view'),
            $transport,
            app('events')
        );

        // IMPORTANT: Send immediately, bypassing queue
        // If the mailable implements ShouldQueue, calling send() would queue it,
        // but we want to send it NOW with our custom SMTP settings
        $mailer->to($to)->sendNow($mailable);
    }

    /**
     * Send email using OAuth-based SMTP (Gmail, etc.)
     */
    protected static function sendWithOAuth(SmtpSetting $settings, $mailable, $to)
    {
        $tokens = $settings->oauth_tokens;

        // Check if we need to refresh the token
        if ($settings->token_expires_at && $settings->token_expires_at->isPast()) {
            $tokens = static::refreshOAuthToken($settings);
            if (!$tokens) {
                \Log::error('Failed to refresh OAuth token for SMTP setting: ' . $settings->id);
                throw new \Exception('OAuth token expired and refresh failed. Please reconnect your email provider.');
            }
        }

        // For Gmail OAuth, we need to use XOAUTH2 authentication
        $accessToken = $tokens['access_token'] ?? null;

        if (!$accessToken) {
            throw new \Exception('No access token available for OAuth SMTP');
        }

        // Configure Gmail transport with OAuth2
        if ($settings->provider === 'gmail') {
            // Use Gmail's official SMTP transport with OAuth2
            $transport = new GmailSmtpTransport($settings->username, $accessToken);

            // Create a custom mailer instance
            $mailer = new Mailer(
                'smtp-oauth',
                app('view'),
                $transport,
                app('events')
            );

            // Send the email immediately, bypassing queue
            $mailer->to($to)->sendNow($mailable);
        } else {
            throw new \Exception('Unsupported OAuth provider: ' . $settings->provider);
        }
    }

    /**
     * Refresh OAuth token for a provider
     */
    protected static function refreshOAuthToken(SmtpSetting $settings)
    {
        if ($settings->provider === 'gmail') {
            $provider = app(\App\Services\EmailProviders\EmailProviderManager::class)
                ->getProvider('gmail');

            try {
                $newTokens = $provider->refreshTokenIfNeeded($settings->oauth_tokens);

                if ($newTokens) {
                    // Update the stored tokens
                    $settings->update([
                        'oauth_tokens' => $newTokens,
                        'token_expires_at' => isset($newTokens['expires_in'])
                            ? now()->addSeconds($newTokens['expires_in'])
                            : null,
                    ]);

                    return $newTokens;
                }

                return $settings->oauth_tokens;
            } catch (\Exception $e) {
                \Log::error('Failed to refresh OAuth token: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }
}
