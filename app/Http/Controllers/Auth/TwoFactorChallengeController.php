<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Display the 2FA challenge view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Handle the 2FA challenge.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($request->session()->get('login.id'));

        // Try TOTP code first
        if ($request->filled('code')) {
            $secret = Crypt::decryptString($user->two_factor_secret);

            if ($this->google2fa->verifyKey($secret, $request->code)) {
                return $this->loginUser($request, $user);
            }

            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        // Try recovery code
        if ($request->filled('recovery_code')) {
            $recoveryCodes = json_decode(
                Crypt::decryptString($user->two_factor_recovery_codes),
                true
            );

            if (in_array($request->recovery_code, $recoveryCodes)) {
                // Remove used recovery code
                $recoveryCodes = array_values(array_diff($recoveryCodes, [$request->recovery_code]));

                $user->forceFill([
                    'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
                ])->save();

                return $this->loginUser($request, $user);
            }

            return back()->withErrors(['recovery_code' => 'Invalid recovery code.']);
        }

        return back()->withErrors(['code' => 'Please provide a verification code or recovery code.']);
    }

    /**
     * Complete the login process.
     */
    protected function loginUser(Request $request, User $user): RedirectResponse
    {
        Auth::login($user, $request->session()->get('login.remember', false));

        $request->session()->forget(['login.id', 'login.remember']);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
