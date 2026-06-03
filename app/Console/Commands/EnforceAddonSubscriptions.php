<?php

namespace App\Console\Commands;

use App\Models\OfficeAddon;
use App\Notifications\AddonExpiredNotification;
use App\Notifications\AddonExpiringNotification;
use Illuminate\Console\Command;

class EnforceAddonSubscriptions extends Command
{
    protected $signature   = 'addons:enforce';
    protected $description = 'Expire ended add-on subscriptions and send expiry warnings';

    public function handle(): int
    {
        $now = now();

        // ── 1. Expire active add-ons whose period ended ────────────────────
        $expired = OfficeAddon::with(['office.users', 'addon'])
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($expired as $officeAddon) {
            $officeAddon->update(['status' => 'expired']);

            $admin = $officeAddon->office?->users()
                ->whereHas('roles', fn ($q) => $q->where('name', 'office_admin'))
                ->first();

            $admin?->notify(new AddonExpiredNotification($officeAddon->addon));
            $this->info("Expired addon '{$officeAddon->addon?->slug}' for office #{$officeAddon->office_id}");
        }

        // ── 2. Warn 3 days before expiry ───────────────────────────────────
        $expiring = OfficeAddon::with(['office.users', 'addon'])
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [
                $now->copy()->addDays(2)->startOfDay(),
                $now->copy()->addDays(3)->endOfDay(),
            ])
            ->get();

        foreach ($expiring as $officeAddon) {
            $daysLeft = max(1, (int) $now->diffInDays($officeAddon->expires_at));

            $admin = $officeAddon->office?->users()
                ->whereHas('roles', fn ($q) => $q->where('name', 'office_admin'))
                ->first();

            $admin?->notify(new AddonExpiringNotification($officeAddon->addon, $daysLeft));
        }

        $this->info("addons:enforce done. Expired: {$expired->count()}, Warnings: {$expiring->count()}");

        return self::SUCCESS;
    }
}
