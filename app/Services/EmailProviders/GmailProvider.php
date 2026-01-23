<?php

namespace App\Services\EmailProviders;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Cache;

class GmailProvider implements EmailProviderInterface
{
    public function getName(): string
    {
        return 'gmail';
    }

    public function getDisplayName(): string
    {
        return 'Gmail / Google Workspace';
    }

    public function getType(): string
    {
        return 'oauth';
    }

    public function getAuthorizationUrl(int $userId): ?string
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(route('smtp-settings.provider.callback', ['provider' => 'gmail']));
        $client->addScope('https://www.googleapis.com/auth/gmail.send');
        $client->addScope('https://www.googleapis.com/auth/userinfo.email');
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        // Store user ID in state
        $state = base64_encode(json_encode(['user_id' => $userId]));
        $client->setState($state);

        return $client->createAuthUrl();
    }

    public function handleCallback(array $callbackData): array
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(route('smtp-settings.provider.callback', ['provider' => 'gmail']));

        // Exchange authorization code for tokens
        $token = $client->fetchAccessTokenWithAuthCode($callbackData['code']);

        if (isset($token['error'])) {
            throw new \Exception($token['error_description'] ?? 'Failed to get access token');
        }

        // Get user email
        $client->setAccessToken($token);
        $oauth = new \Google\Service\Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        return [
            'oauth_tokens' => $token,
            'from_email' => $userInfo->email,
            'from_name' => $userInfo->name ?? $userInfo->email,
            'token_expires_at' => isset($token['expires_in']) 
                ? now()->addSeconds($token['expires_in']) 
                : null,
        ];
    }

    public function getSmtpConfig(array $providerData): array
    {
        return [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => $providerData['from_email'] ?? null,
            'password' => null, // OAuth doesn't use password
        ];
    }

    public function validateCredentials(array $data): bool
    {
        // For OAuth, validation happens during the OAuth flow
        return true;
    }

    /**
     * Refresh OAuth token if needed
     */
    public function refreshTokenIfNeeded(array $tokens): ?array
    {
        if (!isset($tokens['refresh_token'])) {
            return null;
        }

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($tokens);

        if ($client->isAccessTokenExpired()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($tokens['refresh_token']);
            
            if (isset($newToken['error'])) {
                throw new \Exception('Failed to refresh token: ' . $newToken['error_description']);
            }

            return $newToken;
        }

        return null;
    }
}
