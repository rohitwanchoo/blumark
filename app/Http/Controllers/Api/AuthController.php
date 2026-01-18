<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

#[Group('Authentication', 'Endpoints for user registration, login, and token management')]
class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Create a new user account and receive an API token for authentication.
     * The returned token should be used as a Bearer token in the Authorization header
     * for all subsequent authenticated requests.
     *
     * @unauthenticated
     *
     * @response 201 {
     *   "message": "User registered successfully",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "email_verified_at": null,
     *     "credits": 0,
     *     "plan": "free",
     *     "remaining_jobs": 3,
     *     "created_at": "2026-01-17T10:00:00.000000Z"
     *   },
     *   "token": "1|abc123xyz...",
     *   "token_type": "Bearer"
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "The email has already been taken.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login
     *
     * Authenticate with email and password to receive an API token.
     * If the user has 2FA enabled, the response will include `two_factor_required: true`
     * and you must call the `/two-factor-challenge` endpoint to complete login.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Login successful",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "email_verified_at": "2026-01-17T10:00:00.000000Z",
     *     "credits": 10,
     *     "plan": "pro",
     *     "remaining_jobs": 100,
     *     "created_at": "2026-01-17T10:00:00.000000Z"
     *   },
     *   "token": "2|xyz789abc...",
     *   "token_type": "Bearer"
     * }
     * @response 200 scenario="2FA Required" {
     *   "two_factor_required": true,
     *   "message": "Two-factor authentication required."
     * }
     * @response 422 scenario="Invalid credentials" {
     *   "message": "The provided credentials are incorrect.",
     *   "errors": {
     *     "email": ["The provided credentials are incorrect."]
     *   }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Check if 2FA is enabled
        if ($user->hasTwoFactorEnabled()) {
            Auth::logout();

            return response()->json([
                'two_factor_required' => true,
                'message' => 'Two-factor authentication required.',
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Verify two-factor authentication
     *
     * Complete login by providing a valid 2FA code after receiving `two_factor_required: true`
     * from the login endpoint.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Login successful",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com"
     *   },
     *   "token": "2|xyz789abc...",
     *   "token_type": "Bearer"
     * }
     * @response 422 scenario="Invalid code" {
     *   "message": "Invalid verification code."
     * }
     */
    public function verifyTwoFactor(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        Auth::logout();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json(['message' => 'Two-factor authentication is not enabled.'], 422);
        }

        $google2fa = new Google2FA();

        // Try TOTP code first
        if ($request->filled('code')) {
            $secret = Crypt::decryptString($user->two_factor_secret);

            if ($google2fa->verifyKey($secret, $request->code)) {
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'message' => 'Login successful',
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]);
            }

            return response()->json(['message' => 'Invalid verification code.'], 422);
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

                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'message' => 'Login successful',
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]);
            }

            return response()->json(['message' => 'Invalid recovery code.'], 422);
        }

        return response()->json(['message' => 'Please provide a verification code or recovery code.'], 422);
    }

    /**
     * Logout
     *
     * Revoke the current API token. After calling this endpoint, the token
     * used for authentication will no longer be valid.
     *
     * @response 200 {
     *   "message": "Logged out successfully"
     * }
     * @response 401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated."
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current user
     *
     * Retrieve the profile information of the currently authenticated user,
     * including their subscription plan and remaining job credits.
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "email_verified_at": "2026-01-17T10:00:00.000000Z",
     *     "credits": 10,
     *     "plan": "pro",
     *     "remaining_jobs": 100,
     *     "created_at": "2026-01-17T10:00:00.000000Z"
     *   }
     * }
     * @response 401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated."
     * }
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}
