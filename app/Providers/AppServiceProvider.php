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
use App\Models\PowerOfAttorney;
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
use App\Policies\PowerOfAttorneyPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
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

        // super_admin bypasses all policies
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });
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
