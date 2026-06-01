<?php

namespace App\Filament\Widgets;

use App\Services\AIUsageService;
use Filament\Widgets\Widget;

class AIUsageWidget extends Widget
{
    protected static string $view = 'filament.widgets.ai-usage';

    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        // Office staff whose plan includes AI; hidden from super_admin.
        return $user
            && $user->office_id
            && ! $user->hasRole('super_admin')
            && (bool) ($user->office?->activePlan()?->ai_enabled ?? false);
    }

    protected function getViewData(): array
    {
        $office = auth()->user()->office;
        $svc    = app(AIUsageService::class);

        $used  = $svc->requestsUsed($office);
        $limit = $svc->requestsLimit($office); // null = unlimited

        return [
            'used'      => $used,
            'limit'     => $limit,
            'unlimited' => $limit === null,
            'percent'   => $limit ? min(100, (int) round($used / max(1, $limit) * 100)) : 0,
        ];
    }
}
