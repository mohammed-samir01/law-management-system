<?php

use App\Http\Controllers\DocumentPDFController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ImageEditorController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\PlatformContactController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\SaasLandingController;
use App\Http\Controllers\SubscriptionBillingController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// ── Mizan platform marketing pages ──────────────────────────────
Route::get('/', [SaasLandingController::class, 'index'])->name('home');
Route::get('/pricing', [SaasLandingController::class, 'pricing'])->name('pricing');
Route::post('/contact-platform', [PlatformContactController::class, 'store'])->middleware('throttle:contact')->name('platform.contact');

// Each office's public landing page
Route::get('/offices/{slug}', [LandingController::class, 'office'])->name('office.landing');

// Office self-registration (onboarding)
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [OnboardingController::class, 'showPlans'])->name('plans');
    Route::post('/plan', [OnboardingController::class, 'selectPlan'])->name('plan.select');
    Route::get('/setup', [OnboardingController::class, 'showSetup'])->name('setup');
    Route::post('/setup', [OnboardingController::class, 'register'])->middleware('throttle:register')->name('store');
    Route::get('/success', [OnboardingController::class, 'success'])->name('success');
});

// Email verification (OTP) — requires auth, NOT the verified middleware
Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/verify-email', [EmailVerificationController::class, 'verify'])->middleware('throttle:otp')->name('verification.verify');
    Route::post('/verify-email/resend', [EmailVerificationController::class, 'resend'])->middleware('throttle:otp')->name('verification.resend');
});

// Subscription billing (office pays Mizan)
Route::middleware('auth')->group(function () {
    Route::get('/admin/billing', [SubscriptionBillingController::class, 'show'])->name('billing.show');
    Route::post('/admin/billing/checkout', [SubscriptionBillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/admin/billing/callback/{payment}', [SubscriptionBillingController::class, 'callback'])->name('billing.callback');
    Route::view('/subscription/expired', 'subscription.expired')->name('subscription.expired');
});
Route::post('/billing/webhook', [SubscriptionBillingController::class, 'webhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])->name('billing.webhook');

// Image editor — admin only
Route::middleware(['auth', 'throttle:uploads'])->post('/admin/image-editor/save', [ImageEditorController::class, 'save'])->name('image-editor.save');
Route::middleware('auth')->get('/admin/documents/{document}/pdf', [DocumentPDFController::class, 'download'])->name('documents.pdf');
Route::post('/contact', [LandingController::class, 'contact'])->middleware('throttle:contact')->name('contact');

Route::prefix('portal')->name('portal.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
    });

    Route::middleware(['auth', 'client.portal', 'verified.otp'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Invoices & Payment
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{id}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
        Route::get('invoices/{id}/pay/success', [InvoiceController::class, 'success'])->name('invoices.pay.success');
        Route::get('invoices/{id}/pay/failed', [InvoiceController::class, 'failed'])->name('invoices.pay.failed');
    });
});
