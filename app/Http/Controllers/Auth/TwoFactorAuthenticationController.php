<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Display the 2FA settings page.
     */
    public function show(Request $request): View
    {
        return view('auth.two-factor.show', [
            'user' => $request->user(),
            'enabled' => $request->user()->hasTwoFactorEnabled(),
        ]);
    }

    /**
     * Enable 2FA for the user - show QR code.
     */
    public function enable(Request $request): View
    {
        $user = $request->user();

        // Generate new secret
        $secret = $this->google2fa->generateSecretKey();

        // Store encrypted secret temporarily in session
        $request->session()->put('two_factor_secret', Crypt::encryptString($secret));

        // Generate QR code URL
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.two-factor.enable', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Confirm 2FA setup with a code from the authenticator app.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if (!$request->session()->has('two_factor_secret')) {
            return redirect()->route('two-factor.show')
                ->withErrors(['code' => 'Please start the 2FA setup process again.']);
        }

        $secret = Crypt::decryptString($request->session()->get('two_factor_secret'));

        if (!$this->google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        // Generate recovery codes
        $recoveryCodes = Collection::times(8, fn () => Str::random(10) . '-' . Str::random(10));

        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString($recoveryCodes->toJson()),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('two_factor_secret');

        return redirect()->route('two-factor.recovery-codes')
            ->with('recovery_codes', $recoveryCodes->all())
            ->with('success', 'Two-factor authentication has been enabled.');
    }

    /**
     * Show recovery codes.
     */
    public function showRecoveryCodes(Request $request): View
    {
        $user = $request->user();

        // Check if user has 2FA enabled
        if (!$user->hasTwoFactorEnabled()) {
            return view('auth.two-factor.recovery-codes', [
                'recoveryCodes' => [],
                'showSetup' => true,
            ]);
        }

        // Get recovery codes from session (just enabled) or database
        $recoveryCodes = $request->session()->get('recovery_codes')
            ?? json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true);

        return view('auth.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
            'showSetup' => false,
        ]);
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.show')
                ->withErrors(['error' => 'Two-factor authentication is not enabled.']);
        }

        $recoveryCodes = Collection::times(8, fn () => Str::random(10) . '-' . Str::random(10));

        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString($recoveryCodes->toJson()),
        ])->save();

        return redirect()->route('two-factor.recovery-codes')
            ->with('recovery_codes', $recoveryCodes->all())
            ->with('success', 'Recovery codes have been regenerated.');
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()->route('two-factor.show')
            ->with('success', 'Two-factor authentication has been disabled.');
    }
}
