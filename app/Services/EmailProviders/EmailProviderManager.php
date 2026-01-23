<?php

namespace App\Services\EmailProviders;

class EmailProviderManager
{
    protected array $providers = [];

    public function __construct()
    {
        $this->registerProviders();
    }

    protected function registerProviders(): void
    {
        $this->providers = [
            'gmail' => new GmailProvider(),
            'sendgrid' => new SendGridProvider(),
            'mailgun' => new MailgunProvider(),
        ];
    }

    public function getProvider(string $name): ?EmailProviderInterface
    {
        return $this->providers[$name] ?? null;
    }

    public function getAllProviders(): array
    {
        return $this->providers;
    }

    public function getOAuthProviders(): array
    {
        return array_filter($this->providers, fn($provider) => $provider->getType() === 'oauth');
    }

    public function getApiKeyProviders(): array
    {
        return array_filter($this->providers, fn($provider) => $provider->getType() === 'api_key');
    }
}
