<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SubscriptionStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.subscription-status';

    protected static ?int $sort = -3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        // Show only to office staff who have a subscription; hide from super_admin.
        return $user
            && $user->office_id
            && ! $user->hasRole('super_admin')
            && $user->office?->subscription !== null;
    }

    protected function getViewData(): array
    {
        $subscription = auth()->user()->office?->subscription;

        return [
            'subscription' => $subscription,
            'daysLeft'     => $subscription?->daysLeft() ?? 0,
            'planName'     => $subscription?->plan?->getTranslation('name', 'ar') ?? '—',
        ];
    }
}
