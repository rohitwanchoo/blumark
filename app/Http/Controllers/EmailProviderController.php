<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use App\Services\EmailProviders\EmailProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailProviderController extends Controller
{
    protected EmailProviderManager $providerManager;

    public function __construct(EmailProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    /**
     * Show provider selection page
     */
    public function index()
    {
        $providers = $this->providerManager->getAllProviders();

        return view('smtp-settings.providers', compact('providers'));
    }

    /**
     * Initiate OAuth flow for a provider
     */
    public function connect(string $provider)
    {
        $providerInstance = $this->providerManager->getProvider($provider);

        if (!$providerInstance) {
            return redirect()->route('smtp-settings.index')
                ->with('error', 'Invalid email provider');
        }

        if ($providerInstance->getType() !== 'oauth') {
            return redirect()->route('smtp-settings.index')
                ->with('error', 'This provider does not support OAuth');
        }

        $authUrl = $providerInstance->getAuthorizationUrl(Auth::id());

        return redirect($authUrl);
    }

    /**
     * Handle OAuth callback
     */
    public function callback(string $provider, Request $request)
    {
        $providerInstance = $this->providerManager->getProvider($provider);

        if (!$providerInstance) {
            return redirect()->route('smtp-settings.index')
                ->with('error', 'Invalid email provider');
        }

        try {
            // Get user ID from state
            $state = json_decode(base64_decode($request->state), true);
            $userId = $state['user_id'] ?? Auth::id();

            // Verify user
            if ($userId !== Auth::id()) {
                return redirect()->route('smtp-settings.index')
                    ->with('error', 'Invalid authorization state');
            }

            // Handle callback and get provider data
            $data = $providerInstance->handleCallback($request->all());

            // Get SMTP config
            $smtpConfig = $providerInstance->getSmtpConfig($data);

            // Deactivate all other SMTP settings
            Auth::user()->smtpSettings()->update(['is_active' => false]);

            // Create SMTP setting
            $setting = Auth::user()->smtpSettings()->create([
                'name' => $providerInstance->getDisplayName(),
                'provider' => $provider,
                'provider_type' => 'oauth',
                'oauth_tokens' => $data['oauth_tokens'] ?? null,
                'token_expires_at' => $data['token_expires_at'] ?? null,
                'host' => $smtpConfig['host'],
                'port' => $smtpConfig['port'],
                'encryption' => $smtpConfig['encryption'],
                'username' => $smtpConfig['username'],
                'from_email' => $data['from_email'],
                'from_name' => $data['from_name'],
                'is_active' => true,
            ]);

            return redirect()->route('smtp-settings.index')
                ->with('success', "Successfully connected {$providerInstance->getDisplayName()}!");

        } catch (\Exception $e) {
            \Log::error('Provider callback failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('smtp-settings.index')
                ->with('error', 'Failed to connect provider: ' . $e->getMessage());
        }
    }

    /**
     * Store API key-based provider
     */
    public function storeApiKey(Request $request, string $provider)
    {
        $providerInstance = $this->providerManager->getProvider($provider);

        if (!$providerInstance || $providerInstance->getType() !== 'api_key') {
            return redirect()->route('smtp-settings.index')
                ->with('error', 'Invalid provider');
        }

        $validated = $request->validate([
            'from_email' => 'required|email',
            'from_name' => 'required|string',
        ]);

        // Get provider-specific fields
        $providerData = [];
        if (method_exists($providerInstance, 'getConfigFields')) {
            foreach ($providerInstance->getConfigFields() as $field) {
                $providerData[$field['name']] = $request->input($field['name']);
            }
        }

        // Validate credentials
        if (!$providerInstance->validateCredentials($providerData)) {
            return back()->withErrors(['error' => 'Invalid credentials provided']);
        }

        // Get SMTP config
        $smtpConfig = $providerInstance->getSmtpConfig($providerData);

        // Deactivate all other SMTP settings
        Auth::user()->smtpSettings()->update(['is_active' => false]);

        // Create SMTP setting
        $setting = Auth::user()->smtpSettings()->create([
            'name' => $providerInstance->getDisplayName(),
            'provider' => $provider,
            'provider_type' => 'api_key',
            'provider_data' => $providerData,
            'host' => $smtpConfig['host'],
            'port' => $smtpConfig['port'],
            'encryption' => $smtpConfig['encryption'],
            'username' => $smtpConfig['username'],
            'password' => $smtpConfig['password'],
            'from_email' => $validated['from_email'],
            'from_name' => $validated['from_name'],
            'is_active' => true,
        ]);

        return redirect()->route('smtp-settings.index')
            ->with('success', "Successfully configured {$providerInstance->getDisplayName()}!");
    }
}
