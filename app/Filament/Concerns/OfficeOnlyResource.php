<?php

namespace App\Filament\Concerns;

/**
 * Hides an office-operational resource from the platform owner (super_admin).
 *
 * super_admin manages the platform (offices, plans, subscriptions, users,
 * leads, platform settings) — not the day-to-day operations of a single
 * office (cases, clients, invoices, documents, ...). Those belong to office
 * staff and are scoped by office_id.
 *
 * Navigation is hidden for super_admin; underlying access still works via the
 * global Gate::before bypass if ever needed for support.
 */
trait OfficeOnlyResource
{
    public static function shouldRegisterNavigation(): bool
    {
        return ! (auth()->user()?->hasRole('super_admin') ?? false);
    }
}
