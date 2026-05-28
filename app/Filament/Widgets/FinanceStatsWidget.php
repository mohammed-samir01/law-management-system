<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $officeId = auth()->user()?->office_id;

        $totalPaid = Payment::withoutGlobalScopes()
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->where('status', 'completed')
            ->sum('amount');

        $pendingInvoices = Invoice::withoutGlobalScopes()
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->where('status', 'sent')
            ->count();

        $overdueInvoices = Invoice::withoutGlobalScopes()
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->where('status', 'overdue')
            ->count();

        $monthExpenses = Expense::withoutGlobalScopes()
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('المدفوعات المحصلة', number_format($totalPaid, 0) . ' ر.س')
                ->description('إجمالي المدفوعات المكتملة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('الفواتير المعلقة', $pendingInvoices)
                ->description($overdueInvoices > 0 ? "{$overdueInvoices} فاتورة متأخرة" : 'لا توجد فواتير متأخرة')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($overdueInvoices > 0 ? 'danger' : 'warning'),

            Stat::make('مصاريف الشهر', number_format($monthExpenses, 0) . ' ر.س')
                ->description('مصاريف ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('gray'),
        ];
    }
}
