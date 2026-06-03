<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notification;

class EnforceSubscriptions extends Command
{
    protected $signature   = 'subscriptions:enforce';
    protected $description = 'Expire ended subscriptions (with grace), suspend after grace, send warnings';

    // Grace period given to offices after expiry before hard suspension
    private const GRACE_DAYS = 5;

    public function handle(): int
    {
        $now = now();

        // ── 1. Start grace period for newly expired trials ─────────────────
        $expiredTrials = Subscription::with('office.users')
            ->where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', $now)
            ->get();

        foreach ($expiredTrials as $sub) {
            $sub->update([
                'status'        => 'expired',
                'grace_ends_at' => $now->copy()->addDays(self::GRACE_DAYS),
            ]);
            $this->notifyAdmin($sub, new SubscriptionExpiredNotification($sub));
            $this->info("Grace started: office #{$sub->office_id} (trial)");
        }

        // ── 2. Start grace period for newly expired active subscriptions ───
        $expiredActive = Subscription::with('office.users')
            ->where('status', 'active')
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '<', $now)
            ->get();

        foreach ($expiredActive as $sub) {
            $sub->update([
                'status'        => 'expired',
                'grace_ends_at' => $now->copy()->addDays(self::GRACE_DAYS),
            ]);
            $this->notifyAdmin($sub, new SubscriptionExpiredNotification($sub));
            $this->info("Grace started: office #{$sub->office_id} (active)");
        }

        // ── 3. Hard suspend after grace period ends ────────────────────────
        $gracePassed = Subscription::with('office')
            ->where('status', 'expired')
            ->whereNotNull('grace_ends_at')
            ->where('grace_ends_at', '<', $now)
            ->whereNull('cancelled_at')
            ->get();

        foreach ($gracePassed as $sub) {
            $sub->office?->update(['is_active' => false]);
            // Nullify grace_ends_at so this doesn't re-trigger tomorrow
            $sub->update(['grace_ends_at' => null]);
            $this->info("Suspended: office #{$sub->office_id}");
        }

        // ── 4. 3-day warning for trials ────────────────────────────────────
        $this->sendWarnings('trial', 'trial_ends_at', 3, $now);

        // ── 5. 3-day warning for active paid ──────────────────────────────
        $this->sendWarnings('active', 'current_period_end', 3, $now);

        $this->info('subscriptions:enforce completed.');

        return self::SUCCESS;
    }

    private function sendWarnings(string $status, string $field, int $days, $now): void
    {
        $subs = Subscription::with('office.users')
            ->where('status', $status)
            ->whereNotNull($field)
            ->whereBetween($field, [
                $now->copy()->addDays($days - 1)->startOfDay(),
                $now->copy()->addDays($days)->endOfDay(),
            ])
            ->get();

        foreach ($subs as $sub) {
            $daysLeft = max(1, (int) $now->diffInDays($sub->$field));
            $this->notifyAdmin($sub, new SubscriptionExpiringNotification($sub, $daysLeft));
        }
    }

    private function notifyAdmin(Subscription $sub, Notification $notification): void
    {
        $admin = $sub->office?->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'office_admin'))
            ->first();

        $admin?->notify($notification);
    }
}
