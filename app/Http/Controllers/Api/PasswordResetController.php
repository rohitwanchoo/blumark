<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

#[Group('Authentication', 'Endpoints for user registration, login, and token management')]
class PasswordResetController extends Controller
{
    /**
     * Send password reset link
     *
     * Send a password reset link to the user's email address.
     * The link will be valid for a limited time period.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "We have emailed your password reset link."
     * }
     * @response 422 scenario="User not found" {
     *   "message": "We can't find a user with that email address."
     * }
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 422);
    }

    /**
     * Reset password
     *
     * Reset the user's password using the token received via email.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Your password has been reset."
     * }
     * @response 422 scenario="Invalid token" {
     *   "message": "This password reset token is invalid."
     * }
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 422);
    }
}
