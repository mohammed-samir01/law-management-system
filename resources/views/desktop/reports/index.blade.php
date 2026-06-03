@extends('desktop.layouts.app')
@section('title', 'التقارير — ميزان')
@section('page-title', 'التقارير والإحصائيات')

@section('content')
<div class="space-y-5">

    {{-- KPI Row --}}
    <div class="grid grid-cols-4 gap-4">
        @php
        $kpis = [
            ['label' => 'إجمالي الإيرادات',  'value' => number_format($totals['revenue_total'], 0) . ' ج.م', 'color' => 'text-green-600 dark:text-green-400',  'bg' => 'bg-green-50 dark:bg-green-900/20 border-green-100 dark:border-green-800'],
            ['label' => 'مبالغ معلّقة',       'value' => number_format($totals['pending_amount'], 0) . ' ج.م','color' => 'text-amber-600 dark:text-amber-400',  'bg' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800'],
            ['label' => 'قضايا نشطة',         'value' => number_format($totals['active_cases']),              'color' => 'text-blue-600 dark:text-blue-400',    'bg' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-800'],
            ['label' => 'إجمالي الجلسات',     'value' => number_format($totals['total_hearings']),            'color' => 'text-purple-600 dark:text-purple-400','bg' => 'bg-purple-50 dark:bg-purple-900/20 border-purple-100 dark:border-purple-800'],
        ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="rounded-2xl p-4 border {{ $kpi['bg'] }}">
            <p class="text-2xl font-bold {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $kpi['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 gap-5">

        {{-- Monthly Revenue Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4">الإيرادات الشهرية (12 شهراً)</h3>
            <canvas id="revenueChart" height="180"></canvas>
        </div>

        {{-- Cases by Status --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4">القضايا حسب الحالة</h3>
            <div class="flex items-center justify-center">
                <canvas id="casesChart" height="180"></canvas>
            </div>
        </div>

        {{-- Hearings by Month --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4">الجلسات الشهرية (6 أشهر)</h3>
            <canvas id="hearingsChart" height="180"></canvas>
        </div>

        {{-- Cases Status Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4">ملخص القضايا</h3>
            <div class="space-y-3">
                @php
                $statusLabels = ['new' => 'جديدة', 'active' => 'نشطة', 'pending' => 'معلقة', 'closed' => 'مغلقة'];
                $statusColors = ['new' => 'bg-blue-500', 'active' => 'bg-green-500', 'pending' => 'bg-yellow-500', 'closed' => 'bg-gray-400'];
                $totalCases   = $casesByStatus->sum();
                @endphp
                @foreach($statusLabels as $key => $label)
                @php $count = $casesByStatus[$key] ?? 0; $pct = $totalCases ? round($count / $totalCases * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-300">{{ $label }}</span>
                        <span class="font-medium text-gray-700 dark:text-gray-200">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $statusColors[$key] }} rounded-full transition-all"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
const isDark = document.documentElement.classList.contains('dark');
const gridColor = isDark ? '#374151' : '#F3F4F6';
const tickColor = isDark ? '#9CA3AF' : '#6B7280';

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: @json($monthlyRevenue->pluck('month')),
        datasets: [{
            label: 'الإيرادات',
            data: @json($monthlyRevenue->pluck('total')),
            borderColor: '#C9A84C',
            backgroundColor: 'rgba(201,168,76,0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#C9A84C',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: gridColor }, ticks: { color: tickColor } },
            x: { grid: { display: false },  ticks: { color: tickColor } },
        }
    }
});

// Cases Doughnut
new Chart(document.getElementById('casesChart'), {
    type: 'doughnut',
    data: {
        labels: ['جديدة','نشطة','معلقة','مغلقة'],
        datasets: [{
            data: [
                {{ $casesByStatus['new']     ?? 0 }},
                {{ $casesByStatus['active']  ?? 0 }},
                {{ $casesByStatus['pending'] ?? 0 }},
                {{ $casesByStatus['closed']  ?? 0 }},
            ],
            backgroundColor: ['#3B82F6','#22C55E','#EAB308','#9CA3AF'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { color: tickColor, padding: 12, font: { size: 12 } } } }
    }
});

// Hearings Chart
new Chart(document.getElementById('hearingsChart'), {
    type: 'bar',
    data: {
        labels: @json($hearingsByMonth->pluck('month')),
        datasets: [{
            label: 'الجلسات',
            data: @json($hearingsByMonth->pluck('count')),
            backgroundColor: '#1E3A5F',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: gridColor }, ticks: { color: tickColor } },
            x: { grid: { display: false },  ticks: { color: tickColor } },
        }
    }
});
</script>
@endpush
@endsection

