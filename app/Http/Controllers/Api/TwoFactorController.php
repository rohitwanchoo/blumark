<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

#[Group('Two-Factor Authentication', 'Endpoints for managing two-factor authentication')]
class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Get 2FA status
     *
     * Check if two-factor authentication is enabled for the current user.
     *
     * @response 200 {
     *   "enabled": true
     * }
     */
    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'enabled' => $request->user()->hasTwoFactorEnabled(),
        ]);
    }

    /**
     * Enable 2FA
     *
     * Generate a new 2FA secret and QR code URL. The user must then confirm
     * the setup by providing a valid TOTP code.
     *
     * @response 200 {
     *   "secret": "JBSWY3DPEHPK3PXP",
     *   "qr_code_url": "otpauth://totp/BluMark:user@example.com?secret=JBSWY3DPEHPK3PXP&issuer=BluMark"
     * }
     */
    public function enable(Request $request): JsonResponse
    {
        $user = $request->user();
        $secret = $this->google2fa->generateSecretKey();

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Store secret temporarily (will be confirmed later)
        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
        ])->save();

        return response()->json([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    /**
     * Confirm 2FA setup
     *
     * Confirm 2FA setup by providing a valid TOTP code from the authenticator app.
     * Returns recovery codes that should be stored securely.
     *
     * @response 200 {
     *   "message": "Two-factor authentication enabled.",
     *   "recovery_codes": [
     *     "abcdef1234-ghijkl5678",
     *     "mnopqr9012-stuvwx3456"
     *   ]
     * }
     * @response 422 scenario="Invalid code" {
     *   "message": "Invalid verification code."
     * }
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json(['message' => 'Please enable 2FA first.'], 422);
        }

        $secret = Crypt::decryptString($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $request->code)) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        $recoveryCodes = Collection::times(8, fn () => Str::random(10) . '-' . Str::random(10));

        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString($recoveryCodes->toJson()),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Two-factor authentication enabled.',
            'recovery_codes' => $recoveryCodes->all(),
        ]);
    }

    /**
     * Disable 2FA
     *
     * Disable two-factor authentication for the current user.
     * Requires current password for security.
     *
     * @response 200 {
     *   "message": "Two-factor authentication disabled."
     * }
     * @response 422 scenario="Invalid password" {
     *   "message": "The password is incorrect.",
     *   "errors": {
     *     "password": ["The password is incorrect."]
     *   }
     * }
     */
    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return response()->json([
            'message' => 'Two-factor authentication disabled.',
        ]);
    }

    /**
     * Get recovery codes
     *
     * Retrieve the current recovery codes.
     *
     * @response 200 {
     *   "recovery_codes": [
     *     "abcdef1234-ghijkl5678",
     *     "mnopqr9012-stuvwx3456"
     *   ]
     * }
     */
    public function recoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json(['message' => 'Two-factor authentication is not enabled.'], 422);
        }

        return response()->json([
            'recovery_codes' => json_decode(
                Crypt::decryptString($user->two_factor_recovery_codes),
                true
            ),
        ]);
    }

    /**
     * Regenerate recovery codes
     *
     * Generate new recovery codes. Old codes will no longer work.
     *
     * @response 200 {
     *   "message": "Recovery codes regenerated.",
     *   "recovery_codes": [
     *     "newcode1234-newcode5678",
     *     "newcode9012-newcode3456"
     *   ]
     * }
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json(['message' => 'Two-factor authentication is not enabled.'], 422);
        }

        $recoveryCodes = Collection::times(8, fn () => Str::random(10) . '-' . Str::random(10));

        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString($recoveryCodes->toJson()),
        ])->save();

        return response()->json([
            'message' => 'Recovery codes regenerated.',
            'recovery_codes' => $recoveryCodes->all(),
        ]);
    }
}
