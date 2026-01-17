<?php

use App\Http\Controllers\Api\AuthController;
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

    // Protected Routes (require Sanctum token)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Watermark Jobs
        Route::apiResource('watermark-jobs', WatermarkJobController::class);
        Route::get('/watermark-jobs/{watermark_job}/status', [WatermarkJobController::class, 'status']);
        Route::get('/watermark-jobs/{watermark_job}/download', [WatermarkJobController::class, 'download']);
    });
});
