<?php

namespace App\Services\EmailProviders;

class SendGridProvider implements EmailProviderInterface
{
    public function getName(): string
    {
        return 'sendgrid';
    }

    public function getDisplayName(): string
    {
        return 'SendGrid';
    }

    public function getType(): string
    {
        return 'api_key';
    }

    public function getAuthorizationUrl(int $userId): ?string
    {
        // SendGrid uses API keys, no OAuth
        return null;
    }

    public function handleCallback(array $callbackData): array
    {
        // Not used for API key providers
        return [];
    }

    public function getSmtpConfig(array $providerData): array
    {
        return [
            'host' => 'smtp.sendgrid.net',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'apikey',
            'password' => $providerData['api_key'] ?? null,
        ];
    }

    public function validateCredentials(array $data): bool
    {
        // Basic validation - check if API key is provided
        return !empty($data['api_key']) && str_starts_with($data['api_key'], 'SG.');
    }

    /**
     * Get configuration fields for this provider
     */
    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'api_key',
                'label' => 'SendGrid API Key',
                'type' => 'password',
                'placeholder' => 'SG.xxxxxxxxxxxxxxxxxxxxxxxx',
                'help' => 'Get your API key from SendGrid Settings > API Keys',
                'required' => true,
            ],
        ];
    }
}
