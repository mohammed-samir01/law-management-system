<?php

namespace App\Filament\Widgets;

use App\Models\LegalCase;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CasesByStatusChartWidget extends ChartWidget
{
    protected static ?int    $sort    = 3;
    protected static ?string $heading = 'توزيع القضايا حسب الحالة';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $officeId = auth()->user()?->office_id;

        $data = LegalCase::withoutGlobalScopes()
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [
            'active'     => 'نشطة',
            'pending'    => 'معلقة',
            'adjourned'  => 'مؤجلة',
            'closed'     => 'مغلقة',
            'archived'   => 'مؤرشفة',
        ];

        $colors = [
            'active'     => '#22c55e',
            'pending'    => '#f59e0b',
            'adjourned'  => '#3b82f6',
            'closed'     => '#6b7280',
            'archived'   => '#a1a1aa',
        ];

        $chartLabels = [];
        $chartData   = [];
        $chartColors = [];

        foreach ($labels as $key => $label) {
            if (isset($data[$key])) {
                $chartLabels[] = $label;
                $chartData[]   = $data[$key];
                $chartColors[] = $colors[$key];
            }
        }

        return [
            'datasets' => [
                [
                    'data'            => $chartData,
                    'backgroundColor' => $chartColors,
                ],
            ],
            'labels' => $chartLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
