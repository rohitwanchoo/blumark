<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WatermarkJobController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public pricing page
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

// Legal pages
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// Stripe webhooks (must be outside auth middleware)
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('cashier.webhook');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Social authentication routes
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });

    // Watermark Jobs
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [WatermarkJobController::class, 'index'])->name('index');
        Route::get('/batch', [WatermarkJobController::class, 'batch'])->name('batch');
        Route::post('/', [WatermarkJobController::class, 'store'])->name('store');
        Route::get('/{job}', [WatermarkJobController::class, 'show'])->name('show');
        Route::delete('/{job}', [WatermarkJobController::class, 'destroy'])->name('destroy');
        Route::get('/{job}/status', [WatermarkJobController::class, 'status'])->name('status');
        Route::get('/{job}/download', [DownloadController::class, 'download'])->name('download');
        Route::get('/{job}/preview', [DownloadController::class, 'preview'])->name('preview');
    });

    // Billing routes
    Route::prefix('billing')->name('billing.')->group(function () {
        // Billing dashboard
        Route::get('/', [BillingController::class, 'index'])->name('index');

        // Subscription management
        Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
        Route::get('/subscribe/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
        Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::post('/subscription/resume', [SubscriptionController::class, 'resume'])->name('subscription.resume');
        Route::post('/subscription/swap/{plan}', [SubscriptionController::class, 'swap'])->name('subscription.swap');

        // Credit management
        Route::get('/credits', [CreditController::class, 'index'])->name('credits');
        Route::get('/credits/purchase/{creditPack}', [CreditController::class, 'purchase'])->name('credits.purchase');
        Route::get('/credits/success', [CreditController::class, 'success'])->name('credits.success');

        // Payment methods
        Route::get('/payment-methods', [BillingController::class, 'paymentMethods'])->name('payment-methods');
        Route::post('/payment-methods', [BillingController::class, 'addPaymentMethod'])->name('payment-methods.store');
        Route::delete('/payment-methods/{paymentMethod}', [BillingController::class, 'removePaymentMethod'])->name('payment-methods.destroy');
        Route::post('/payment-methods/{paymentMethod}/default', [BillingController::class, 'setDefaultPaymentMethod'])->name('payment-methods.default');

        // Invoices
        Route::get('/invoices', [BillingController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [BillingController::class, 'downloadInvoice'])->name('invoices.download');
    });
});
