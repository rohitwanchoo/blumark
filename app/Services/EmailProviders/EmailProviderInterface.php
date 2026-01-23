<?php

namespace App\Services\EmailProviders;

interface EmailProviderInterface
{
    /**
     * Get provider name
     */
    public function getName(): string;

    /**
     * Get provider display name
     */
    public function getDisplayName(): string;

    /**
     * Get provider type (oauth, api_key, custom)
     */
    public function getType(): string;

    /**
     * Get authorization URL for OAuth providers
     */
    public function getAuthorizationUrl(int $userId): ?string;

    /**
     * Handle OAuth callback
     */
    public function handleCallback(array $callbackData): array;

    /**
     * Get SMTP configuration from provider data
     */
    public function getSmtpConfig(array $providerData): array;

    /**
     * Validate provider credentials
     */
    public function validateCredentials(array $data): bool;
}
