<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\TwoFactorController;
use App\Http\Controllers\Api\WatermarkJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // Public Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset Routes
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);

    // Two-Factor Authentication Challenge (during login)
    Route::post('/two-factor-challenge', [AuthController::class, 'verifyTwoFactor']);

    // Protected Routes (require Sanctum token)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Email Verification
        Route::prefix('email')->group(function () {
            Route::get('/verification-status', [EmailVerificationController::class, 'status']);
            Route::post('/verification-notification', [EmailVerificationController::class, 'resend']);
            Route::get('/verify/{id}/{hash}', [EmailVerificationController::class, 'verify']);
        });

        // Two-Factor Authentication Settings
        Route::prefix('two-factor')->group(function () {
            Route::get('/status', [TwoFactorController::class, 'status']);
            Route::post('/enable', [TwoFactorController::class, 'enable']);
            Route::post('/confirm', [TwoFactorController::class, 'confirm']);
            Route::delete('/disable', [TwoFactorController::class, 'disable']);
            Route::get('/recovery-codes', [TwoFactorController::class, 'recoveryCodes']);
            Route::post('/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes']);
        });

        // Watermark Jobs
        Route::apiResource('watermark-jobs', WatermarkJobController::class);
        Route::get('/watermark-jobs/{watermark_job}/status', [WatermarkJobController::class, 'status']);
        Route::get('/watermark-jobs/{watermark_job}/download', [WatermarkJobController::class, 'download']);
    });
});
