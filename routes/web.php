<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WatermarkJobController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\LenderController;
use App\Http\Controllers\LenderDistributionController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\AlertController as AdminAlertController;
use App\Http\Controllers\Admin\AuditController as AdminAuditController;
use App\Http\Controllers\Admin\OcrTestController as AdminOcrTestController;
use App\Http\Controllers\VerificationController;
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

// Public shared link routes (no auth required)
Route::get('/s/{token}', [ShareController::class, 'show'])->name('share.show');
Route::get('/s/{token}/download', fn(string $token) => redirect()->route('share.show', $token));
Route::post('/s/{token}/download', [ShareController::class, 'download'])->name('share.download');

// Public document verification routes (no auth required)
Route::prefix('verify')->name('verify.')->group(function () {
    Route::get('/', [VerificationController::class, 'index'])->name('index');
    Route::post('/upload', [VerificationController::class, 'upload'])->name('upload');
    Route::post('/qr', [VerificationController::class, 'qr'])->name('qr');
    Route::post('/tamper-report', [VerificationController::class, 'tamperReport'])->name('tamper-report');
    Route::get('/job/{id}', [VerificationController::class, 'showByJob'])->name('show-job');
    Route::get('/{token}', [VerificationController::class, 'show'])->name('show');
});

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

    // Password reset routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    // Two-factor authentication challenge (during login)
    Route::get('two-factor-challenge', [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
    Route::post('two-factor-challenge', [TwoFactorChallengeController::class, 'store']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Email Verification
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Dashboard (requires verified email)
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    // Profile (accessible without verification to allow email updates)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // Two-factor authentication settings
    Route::prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('/', [TwoFactorAuthenticationController::class, 'show'])->name('show');
        Route::get('/enable', [TwoFactorAuthenticationController::class, 'enable'])->name('enable');
        Route::post('/confirm', [TwoFactorAuthenticationController::class, 'confirm'])->name('confirm');
        Route::get('/recovery-codes', [TwoFactorAuthenticationController::class, 'showRecoveryCodes'])->name('recovery-codes');
        Route::post('/recovery-codes', [TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes'])->name('recovery-codes.regenerate');
        Route::delete('/', [TwoFactorAuthenticationController::class, 'disable'])->name('disable');
    });

    // Watermark Jobs (requires verified email)
    Route::prefix('jobs')->name('jobs.')->middleware('verified')->group(function () {
        Route::get('/', [WatermarkJobController::class, 'index'])->name('index');
        Route::get('/batch', [WatermarkJobController::class, 'batch'])->name('batch');
        Route::post('/', [WatermarkJobController::class, 'store'])->name('store');
        Route::get('/{job}', [WatermarkJobController::class, 'show'])->name('show');
        Route::delete('/{job}', [WatermarkJobController::class, 'destroy'])->name('destroy');
        Route::get('/{job}/status', [WatermarkJobController::class, 'status'])->name('status');
        Route::get('/{job}/download', [DownloadController::class, 'download'])->name('download');
        Route::get('/{job}/preview', [DownloadController::class, 'preview'])->name('preview');
        Route::post('/{job}/share', [ShareController::class, 'store'])->name('share');
        Route::get('/{job}/shares', [ShareController::class, 'listForJob'])->name('shares');
    });

    // Batch processing (requires verified email)
    Route::prefix('batch')->name('batch.')->middleware('verified')->group(function () {
        Route::get('/', [BatchController::class, 'index'])->name('index');
        Route::get('/create', [BatchController::class, 'create'])->name('create');
        Route::post('/', [BatchController::class, 'store'])->name('store');
        Route::get('/{batch}', [BatchController::class, 'show'])->name('show');
        Route::get('/{batch}/status', [BatchController::class, 'status'])->name('status');
        Route::get('/{batch}/download', [BatchController::class, 'download'])->name('download');
        Route::delete('/{batch}', [BatchController::class, 'destroy'])->name('destroy');
    });

    // Templates (requires verified email)
    Route::prefix('templates')->name('templates.')->middleware('verified')->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::get('/list', [TemplateController::class, 'list'])->name('list');
        Route::post('/', [TemplateController::class, 'store'])->name('store');
        Route::post('/quick', [TemplateController::class, 'quickSave'])->name('quick');
        Route::put('/{template}', [TemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [TemplateController::class, 'destroy'])->name('destroy');
    });

    // Shared links management (requires verified email)
    Route::prefix('shares')->name('shares.')->middleware('verified')->group(function () {
        Route::get('/', [ShareController::class, 'index'])->name('index');
        Route::delete('/{sharedLink}', [ShareController::class, 'destroy'])->name('destroy');
    });

    // Lenders (requires verified email)
    Route::prefix('lenders')->name('lenders.')->middleware('verified')->group(function () {
        Route::get('/', [LenderController::class, 'index'])->name('index');
        Route::get('/create', [LenderController::class, 'create'])->name('create');
        Route::post('/', [LenderController::class, 'store'])->name('store');
        Route::get('/list', [LenderController::class, 'list'])->name('list');
        Route::get('/{lender}/edit', [LenderController::class, 'edit'])->name('edit');
        Route::put('/{lender}', [LenderController::class, 'update'])->name('update');
        Route::delete('/{lender}', [LenderController::class, 'destroy'])->name('destroy');
    });

    // Lender Distributions (requires verified email)
    Route::prefix('distributions')->name('distributions.')->middleware('verified')->group(function () {
        Route::get('/', [LenderDistributionController::class, 'index'])->name('index');
        Route::get('/create', [LenderDistributionController::class, 'create'])->name('create');
        Route::post('/', [LenderDistributionController::class, 'store'])->name('store');
        Route::get('/{distribution}', [LenderDistributionController::class, 'show'])->name('show');
        Route::get('/{distribution}/status', [LenderDistributionController::class, 'status'])->name('status');
        Route::post('/{distribution}/send-all', [LenderDistributionController::class, 'sendAll'])->name('send-all');
        Route::get('/{distribution}/download-all', [LenderDistributionController::class, 'downloadAll'])->name('download-all');
        Route::delete('/{distribution}', [LenderDistributionController::class, 'destroy'])->name('destroy');
        Route::get('/{distribution}/items/{item}/download', [LenderDistributionController::class, 'itemDownload'])->name('item.download');
        Route::post('/{distribution}/items/{item}/send', [LenderDistributionController::class, 'itemSend'])->name('item.send');
    });

    // Email Templates (requires verified email)
    Route::prefix('email-templates')->name('email-templates.')->middleware('verified')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::get('/create', [EmailTemplateController::class, 'create'])->name('create');
        Route::post('/', [EmailTemplateController::class, 'store'])->name('store');
        Route::get('/{template}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [EmailTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [EmailTemplateController::class, 'destroy'])->name('destroy');
        Route::post('/{template}/make-default', [EmailTemplateController::class, 'makeDefault'])->name('make-default');
        Route::post('/preview', [EmailTemplateController::class, 'preview'])->name('preview');
    });

    // SMTP Settings (requires verified email)
    Route::prefix('smtp-settings')->name('smtp-settings.')->middleware('verified')->group(function () {
        Route::get('/', [App\Http\Controllers\SmtpSettingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\SmtpSettingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SmtpSettingController::class, 'store'])->name('store');
        Route::get('/{smtpSetting}/edit', [App\Http\Controllers\SmtpSettingController::class, 'edit'])->name('edit');
        Route::put('/{smtpSetting}', [App\Http\Controllers\SmtpSettingController::class, 'update'])->name('update');
        Route::delete('/{smtpSetting}', [App\Http\Controllers\SmtpSettingController::class, 'destroy'])->name('destroy');
        Route::post('/{smtpSetting}/test', [App\Http\Controllers\SmtpSettingController::class, 'test'])->name('test');
        Route::post('/{smtpSetting}/activate', [App\Http\Controllers\SmtpSettingController::class, 'activate'])->name('activate');

        // Provider-based connections
        Route::get('/providers', [App\Http\Controllers\EmailProviderController::class, 'index'])->name('providers');
        Route::get('/provider/{provider}/connect', [App\Http\Controllers\EmailProviderController::class, 'connect'])->name('provider.connect');
        Route::get('/provider/{provider}/callback', [App\Http\Controllers\EmailProviderController::class, 'callback'])->name('provider.callback');
        Route::post('/provider/{provider}/api-key', [App\Http\Controllers\EmailProviderController::class, 'storeApiKey'])->name('provider.api-key');

        // Help page
        Route::get('/help', function () {
            return view('smtp-settings.help');
        })->name('help');
    });

    // Billing routes (requires verified email)
    Route::prefix('billing')->name('billing.')->middleware('verified')->group(function () {
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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/activity', [AdminDashboardController::class, 'activity'])->name('activity');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/credits', [AdminUserController::class, 'addCredits'])->name('users.credits');
    Route::post('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');

    // Impersonation
    Route::post('/users/{user}/impersonate', [ImpersonationController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/stop-impersonating', [ImpersonationController::class, 'stop'])->name('impersonate.stop');

    // Jobs
    Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [AdminJobController::class, 'show'])->name('jobs.show');
    Route::delete('/jobs/{job}', [AdminJobController::class, 'destroy'])->name('jobs.destroy');

    // Security Audit
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AdminAuditController::class, 'index'])->name('index');
        Route::get('/leaks', [AdminAuditController::class, 'leaks'])->name('leaks');
        Route::get('/verifications', [AdminAuditController::class, 'verifications'])->name('verifications');
        Route::get('/job/{job}', [AdminAuditController::class, 'job'])->name('job');
        Route::get('/job/{job}/export', [AdminAuditController::class, 'export'])->name('export');
        Route::get('/investigate', fn() => redirect()->route('admin.audit.leaks'))->name('investigate.form');
        Route::post('/investigate', [AdminAuditController::class, 'investigate'])->name('investigate');
        Route::get('/fingerprint/{fingerprint}', [AdminAuditController::class, 'fingerprint'])->name('fingerprint');
    });

    // OCR Testing
    Route::prefix('ocr')->name('ocr.')->group(function () {
        Route::get('/', [AdminOcrTestController::class, 'index'])->name('index');
        Route::post('/job/{job}/test', [AdminOcrTestController::class, 'test'])->name('test');
        Route::post('/job/{job}/compare', [AdminOcrTestController::class, 'compare'])->name('compare');
        Route::post('/batch-test', [AdminOcrTestController::class, 'batchTest'])->name('batch');
        Route::get('/job/{job}/history', [AdminOcrTestController::class, 'history'])->name('history');
        Route::delete('/result/{result}', [AdminOcrTestController::class, 'destroy'])->name('destroy');
    });

    // Security Alerts
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [AdminAlertController::class, 'index'])->name('index');
        Route::get('/{alert}', [AdminAlertController::class, 'show'])->name('show');
        Route::post('/{alert}/acknowledge', [AdminAlertController::class, 'acknowledge'])->name('acknowledge');
        Route::post('/{alert}/resolve', [AdminAlertController::class, 'resolve'])->name('resolve');
        Route::post('/{alert}/dismiss', [AdminAlertController::class, 'dismiss'])->name('dismiss');
    });
});
