<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Console\Command;

class WarnExpiringSubscriptions extends Command
{
    protected $signature = 'subscriptions:warn {--days=7 : Days before expiry to send warning}';

    protected $description = 'Send expiry warning emails N days before subscription ends';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $now  = now();

        $window = [
            $now->copy()->addDays($days - 1)->startOfDay(),
            $now->copy()->addDays($days)->endOfDay(),
        ];

        $subs = Subscription::with('office.users')
            ->whereIn('status', ['trial', 'active'])
            ->where(function ($q) use ($window) {
                $q->whereBetween('trial_ends_at', $window)
                  ->orWhereBetween('current_period_end', $window);
            })
            ->get();

        foreach ($subs as $sub) {
            $endsAt   = $sub->trial_ends_at ?? $sub->current_period_end;
            $daysLeft = max(1, (int) $now->diffInDays($endsAt));

            $admin = $sub->office?->users()
                ->whereHas('roles', fn ($q) => $q->where('name', 'office_admin'))
                ->first();

            $admin?->notify(new SubscriptionExpiringNotification($sub, $daysLeft));
        }

        $this->info("Sent {$days}-day expiry warnings to {$subs->count()} offices.");

        return self::SUCCESS;
    }
}
