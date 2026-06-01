<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class EnforceSubscriptions extends Command
{
    protected $signature = 'subscriptions:enforce';

    protected $description = 'Mark trial/active subscriptions whose period has ended as expired';

    public function handle(): int
    {
        $now = now();

        // Trials whose trial window ended.
        $trials = Subscription::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', $now)
            ->update(['status' => 'expired']);

        // Active subscriptions whose paid period ended.
        $active = Subscription::where('status', 'active')
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '<', $now)
            ->update(['status' => 'expired']);

        $this->info("Expired trials: {$trials}, expired active: {$active}");

        return self::SUCCESS;
    }
}
