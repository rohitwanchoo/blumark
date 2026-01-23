<?php

namespace App\Services\EmailProviders;

class MailgunProvider implements EmailProviderInterface
{
    public function getName(): string
    {
        return 'mailgun';
    }

    public function getDisplayName(): string
    {
        return 'Mailgun';
    }

    public function getType(): string
    {
        return 'api_key';
    }

    public function getAuthorizationUrl(int $userId): ?string
    {
        return null;
    }

    public function handleCallback(array $callbackData): array
    {
        return [];
    }

    public function getSmtpConfig(array $providerData): array
    {
        $region = $providerData['region'] ?? 'us';
        $host = $region === 'eu' ? 'smtp.eu.mailgun.org' : 'smtp.mailgun.org';

        return [
            'host' => $host,
            'port' => 587,
            'encryption' => 'tls',
            'username' => $providerData['smtp_username'] ?? null,
            'password' => $providerData['smtp_password'] ?? null,
        ];
    }

    public function validateCredentials(array $data): bool
    {
        return !empty($data['smtp_username']) && !empty($data['smtp_password']);
    }

    /**
     * Get configuration fields for this provider
     */
    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'smtp_username',
                'label' => 'SMTP Username',
                'type' => 'text',
                'placeholder' => 'postmaster@your-domain.mailgun.org',
                'help' => 'Found in Mailgun Dashboard > Sending > Domain Settings',
                'required' => true,
            ],
            [
                'name' => 'smtp_password',
                'label' => 'SMTP Password',
                'type' => 'password',
                'placeholder' => 'Enter your SMTP password',
                'help' => 'Default SMTP password from Mailgun',
                'required' => true,
            ],
            [
                'name' => 'region',
                'label' => 'Region',
                'type' => 'select',
                'options' => [
                    'us' => 'US (smtp.mailgun.org)',
                    'eu' => 'EU (smtp.eu.mailgun.org)',
                ],
                'default' => 'us',
                'required' => true,
            ],
        ];
    }
}
