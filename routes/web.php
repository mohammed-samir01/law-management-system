<?php

use App\Http\Controllers\AddonBillingController;
use App\Http\Controllers\DocumentPDFController;
use App\Http\Controllers\DocumentSigningController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ImageEditorController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\PlatformContactController;
use App\Http\Controllers\Portal\InvoiceController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\SaasLandingController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\SubscriptionBillingController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// ── Public calendar ICS feed (token-gated, no auth) ─────────────
Route::get('/calendar/{token}.ics', [\App\Http\Controllers\CalendarFeedController::class, 'feed'])->name('calendar.feed');

// ── Public e-signature (token-gated, no auth) ───────────────────
Route::get('/sign/{token}', [DocumentSigningController::class, 'show'])->name('documents.sign.show');
Route::post('/sign/{token}', [DocumentSigningController::class, 'store'])->name('documents.sign.store');

// ── Mizan platform marketing pages ──────────────────────────────
Route::get('/', [SaasLandingController::class, 'index'])->name('home');
Route::get('/pricing', [SaasLandingController::class, 'pricing'])->name('pricing');
Route::post('/contact-platform', [PlatformContactController::class, 'store'])->middleware('throttle:contact')->name('platform.contact');

// SEO
Route::get('/robots.txt', function () {
    $content = implode("\n", [
        'User-agent: *',
        'Disallow: /admin/',
        'Disallow: /portal/',
        'Disallow: /register/',
        'Disallow: /verify-email',
        'Disallow: /admin/billing',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ]);
    return response($content, 200)->header('Content-Type', 'text/plain');
});
Route::get('/sitemap.xml', [SaasLandingController::class, 'sitemap'])->name('sitemap');
Route::get('/sitemap-pages.xml', [SaasLandingController::class, 'sitemapPages'])->name('sitemap.pages');
Route::get('/offices/{slug}/sitemap.xml', [LandingController::class, 'sitemap'])->name('office.sitemap');

// Each office's public landing page
Route::get('/offices/{slug}', [LandingController::class, 'office'])->name('office.landing');

// Office self-registration (onboarding)
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [OnboardingController::class, 'showPlans'])->name('plans');
    Route::post('/plan', [OnboardingController::class, 'selectPlan'])->name('plan.select');
    Route::get('/setup', [OnboardingController::class, 'showSetup'])->name('setup');
    Route::post('/setup', [OnboardingController::class, 'register'])->middleware('throttle:register')->name('store');
    Route::get('/profile', [OnboardingController::class, 'showProfileSetup'])->middleware('auth')->name('profile');
    Route::post('/profile', [OnboardingController::class, 'saveProfileSetup'])->middleware('auth')->name('profile.save');
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

// Add-on billing (office activates paid add-ons)
Route::middleware('auth')->group(function () {
    Route::post('/admin/addons/checkout/{addon}', [AddonBillingController::class, 'checkout'])->name('addons.checkout');
    Route::get('/admin/addons/callback/{addonPayment}', [AddonBillingController::class, 'callback'])->name('addons.callback');
});
Route::post('/addons/webhook', [AddonBillingController::class, 'webhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])->name('addons.webhook');

// Telegram bot webhook (secret-gated, no CSRF) — captures client chat_id on /start
Route::post('/telegram/webhook/{secret}', [TelegramWebhookController::class, 'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class])->name('telegram.webhook');

// Image editor — admin only
Route::middleware(['auth', 'throttle:uploads'])->post('/admin/image-editor/save', [ImageEditorController::class, 'save'])->name('image-editor.save');
Route::middleware('auth')->get('/admin/documents/{document}/pdf', [DocumentPDFController::class, 'download'])->name('documents.pdf');
Route::post('/contact', [LandingController::class, 'contact'])->middleware('throttle:contact')->name('contact');

Route::prefix('portal')->name('portal.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
    });

    Route::middleware(['auth', 'client.portal', 'verified.otp', 'portal.locale'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Language switch — persists per client in users.language
        Route::post('locale', function (\Illuminate\Http\Request $request) {
            $lang = $request->input('lang');
            if (in_array($lang, ['ar', 'en'], true) && $request->user()) {
                $request->user()->forceFill(['language' => $lang])->save();
            }
            return back();
        })->name('locale');

        // Invoices & Payment
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{id}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
        Route::get('invoices/{id}/pay/success', [InvoiceController::class, 'success'])->name('invoices.pay.success');
        Route::get('invoices/{id}/pay/failed', [InvoiceController::class, 'failed'])->name('invoices.pay.failed');

        // PWA (client-pwa addon) — manifest, service worker, generated icons
        Route::get('manifest.webmanifest', [PwaController::class, 'manifest'])->name('pwa.manifest');
        Route::get('service-worker.js', [PwaController::class, 'serviceWorker'])->name('pwa.sw');
        Route::get('pwa-icon/{size}', [PwaController::class, 'icon'])->name('pwa.icon');
    });
});
