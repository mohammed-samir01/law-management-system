<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Hearing;
use App\Models\LegalCase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeCases  = LegalCase::withoutGlobalScopes()->where('status', 'active')->count();
        $totalCases   = LegalCase::withoutGlobalScopes()->count();
        $totalClients = Client::withoutGlobalScopes()->count();
        $upcomingHearings = Hearing::withoutGlobalScopes()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->count();

        return [
            Stat::make('القضايا النشطة', $activeCases)
                ->description("من أصل {$totalCases} قضية")
                ->descriptionIcon('heroicon-m-scale')
                ->color('success'),
            Stat::make('العملاء', $totalClients)
                ->description('إجمالي العملاء المسجلين')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('الجلسات القادمة', $upcomingHearings)
                ->description('جلسات مجدولة قادمة')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
        ];
    }
}
