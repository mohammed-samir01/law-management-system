<?php

namespace App\Providers;

use App\Models\AIResult;
use App\Models\CaseLaw;
use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\EnforcementFile;
use App\Models\Expense;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\Legislation;
use App\Models\Office;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\PlatformLead;
use App\Models\PowerOfAttorney;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use App\Observers\DocumentObserver;
use App\Observers\LegalCaseObserver;
use App\Observers\PaymentObserver;
use App\Observers\TicketReplyObserver;
use App\Models\TicketReply;
use App\Policies\AIResultPolicy;
use App\Policies\CaseLawPolicy;
use App\Policies\ClientPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\DocumentTemplatePolicy;
use App\Policies\EnforcementFilePolicy;
use App\Policies\ExpensePolicy;
use App\Policies\HearingPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\LegalCasePolicy;
use App\Policies\LegislationPolicy;
use App\Policies\OfficePolicy;
use App\Policies\PaymentGatewayPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PlanPolicy;
use App\Policies\PlatformLeadPolicy;
use App\Policies\PowerOfAttorneyPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\UserPolicy;
use App\Models\PlatformSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // On Android (NativePHP SQLite), run pending migrations automatically
        if (config('database.default') === 'sqlite') {
            $this->runMigrationsOnAndroid();
        }

        // Observers
        LegalCase::observe(LegalCaseObserver::class);
        Payment::observe(PaymentObserver::class);
        Document::observe(DocumentObserver::class);
        TicketReply::observe(TicketReplyObserver::class);

        // Policies
        Gate::policy(Office::class,           OfficePolicy::class);
        Gate::policy(User::class,             UserPolicy::class);
        Gate::policy(Client::class,           ClientPolicy::class);
        Gate::policy(LegalCase::class,        LegalCasePolicy::class);
        Gate::policy(Hearing::class,          HearingPolicy::class);
        Gate::policy(Document::class,         DocumentPolicy::class);
        Gate::policy(DocumentTemplate::class, DocumentTemplatePolicy::class);
        Gate::policy(Expense::class,          ExpensePolicy::class);
        Gate::policy(Payment::class,          PaymentPolicy::class);
        Gate::policy(Invoice::class,          InvoicePolicy::class);
        Gate::policy(PaymentGateway::class,   PaymentGatewayPolicy::class);
        Gate::policy(EnforcementFile::class,  EnforcementFilePolicy::class);
        Gate::policy(PowerOfAttorney::class,  PowerOfAttorneyPolicy::class);
        Gate::policy(Legislation::class,      LegislationPolicy::class);
        Gate::policy(CaseLaw::class,          CaseLawPolicy::class);
        Gate::policy(SupportTicket::class,    SupportTicketPolicy::class);
        Gate::policy(AIResult::class,         AIResultPolicy::class);
        Gate::policy(Plan::class,             PlanPolicy::class);
        Gate::policy(Subscription::class,     SubscriptionPolicy::class);
        Gate::policy(PlatformLead::class,     PlatformLeadPolicy::class);

        // super_admin bypasses all policies
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        $this->configureRateLimiters();
        $this->applyDynamicMailConfig();
    }

    /**
     * Named rate limiters — all read their limits from the dashboard-managed
     * platform settings (with safe defaults).
     */
    private function configureRateLimiters(): void
    {
        $byUserOrIp = fn ($request) => optional($request->user())->id ?: $request->ip();

        RateLimiter::for('login',    fn ($r) => Limit::perMinute((int) PlatformSetting::get('security.rate.login', 5))->by($r->ip()));
        RateLimiter::for('register', fn ($r) => Limit::perMinute((int) PlatformSetting::get('security.rate.register', 3))->by($r->ip()));
        RateLimiter::for('contact',  fn ($r) => Limit::perMinute((int) PlatformSetting::get('security.rate.contact', 5))->by($r->ip()));
        RateLimiter::for('otp',      fn ($r) => Limit::perMinute((int) PlatformSetting::get('security.rate.otp', 3))->by($byUserOrIp($r)));
        RateLimiter::for('uploads',  fn ($r) => Limit::perMinute((int) PlatformSetting::get('security.rate.uploads', 30))->by($byUserOrIp($r)));
        RateLimiter::for('ai',       fn ($r) => Limit::perMinute((int) PlatformSetting::get('security.rate.ai', 20))->by($byUserOrIp($r)));
        RateLimiter::for('api',      fn ($r) => Limit::perMinute((int) PlatformSetting::get('security.rate.api', 120))->by($byUserOrIp($r)));
    }

    /**
     * Apply dashboard-managed SMTP settings at runtime (encrypted in DB),
     * falling back to .env. Guarded so it never breaks artisan/migrate.
     */
    private function applyDynamicMailConfig(): void
    {
        try {
            if (! Schema::hasTable('platform_settings')) {
                return;
            }

            $mail = PlatformSetting::mail();
            if (empty($mail) || empty($mail['host'])) {
                return;
            }

            Config::set('mail.mailers.smtp', array_filter([
                'transport'  => 'smtp',
                'host'       => $mail['host'] ?? null,
                'port'       => $mail['port'] ?? 587,
                'username'   => $mail['username'] ?? null,
                'password'   => $mail['password'] ?? null,
                'encryption' => $mail['encryption'] ?? 'tls',
            ], fn ($v) => $v !== null));

            Config::set('mail.default', 'smtp');

            if (! empty($mail['from_address'])) {
                Config::set('mail.from.address', $mail['from_address']);
                Config::set('mail.from.name', $mail['from_name'] ?? 'ميزان');
            }
        } catch (\Throwable) {
            // Never break the app boot if settings are unavailable.
        }
    }

    private function runMigrationsOnAndroid(): void
    {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable) {
            // Database not accessible yet — NativePHP will retry on next request
        }
    }
}
