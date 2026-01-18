<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Email Verification', 'Endpoints for email verification management')]
class EmailVerificationController extends Controller
{
    /**
     * Get email verification status
     *
     * Check if the authenticated user's email has been verified.
     *
     * @response 200 {
     *   "verified": true,
     *   "email": "john@example.com",
     *   "email_verified_at": "2026-01-17T10:00:00.000000Z"
     * }
     * @response 200 scenario="Not verified" {
     *   "verified": false,
     *   "email": "john@example.com",
     *   "email_verified_at": null
     * }
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'verified' => $user->hasVerifiedEmail(),
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
        ]);
    }

    /**
     * Resend verification email
     *
     * Send a new email verification notification to the authenticated user.
     * Only works if the user's email is not already verified.
     *
     * @response 200 {
     *   "message": "Verification link sent."
     * }
     * @response 200 scenario="Already verified" {
     *   "message": "Email already verified."
     * }
     */
    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent.',
        ]);
    }

    /**
     * Verify email with token
     *
     * Verify the user's email address using the verification token from the email link.
     * The hash should be the SHA-256 hash of the user's email address.
     *
     * @response 200 {
     *   "message": "Email verified successfully.",
     *   "verified": true
     * }
     * @response 200 scenario="Already verified" {
     *   "message": "Email already verified.",
     *   "verified": true
     * }
     * @response 403 scenario="Invalid hash" {
     *   "message": "Invalid verification link."
     * }
     */
    public function verify(Request $request, string $id, string $hash): JsonResponse
    {
        $user = $request->user();

        // Verify that the ID matches the authenticated user
        if ((string) $user->id !== $id) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 403);
        }

        // Verify the hash
        if (!hash_equals(sha1($user->email), $hash)) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
                'verified' => true,
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Email verified successfully.',
            'verified' => true,
        ]);
    }
}
