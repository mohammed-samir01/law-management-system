@extends('desktop.layouts.app')
@section('title', 'لوحة التحكم — ميزان')
@section('page-title', 'لوحة التحكم')

@section('content')
<div class="space-y-6">

    {{-- Stats Row --}}
    <div class="grid grid-cols-4 gap-5">
        @php
        $cards = [
            ['label' => 'إجمالي القضايا',   'value' => number_format($stats['cases']),                  'color' => 'bg-blue-500',  'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
            ['label' => 'الموكلون',          'value' => number_format($stats['clients']),                'color' => 'bg-purple-500', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'جلسات قادمة',      'value' => number_format($stats['hearings']),               'color' => 'bg-amber-500',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'الإيرادات المحصّلة','value' => number_format($stats['revenue'], 0) . ' ج.م',   'color' => 'bg-green-500',  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
            <div class="{{ $card['color'] }} w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $card['value'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- Revenue Chart --}}
        <div class="col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4">الإيرادات الشهرية</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        {{-- Upcoming Hearings --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 mb-4">الجلسات القادمة</h3>
            <div class="space-y-3">
                @forelse($upcomingHearings as $hearing)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#1E3A5F] rounded-xl flex flex-col items-center justify-center shrink-0 text-white">
                        <span class="font-bold text-sm leading-none">{{ $hearing->scheduled_at->format('d') }}</span>
                        <span class="text-[10px] opacity-70">{{ $hearing->scheduled_at->format('M') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">
                            {{ $hearing->legalCase?->getTranslation('title','ar',false) ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $hearing->scheduled_at->format('H:i') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">لا توجد جلسات قادمة</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Cases --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-bold text-gray-700 dark:text-gray-200">أحدث القضايا</h3>
            <a href="{{ route('desktop.cases') }}" class="text-sm text-[#C9A84C] hover:underline">عرض الكل</a>
        </div>
        <table class="w-full">
            <thead>
                <tr class="text-right text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                    <th class="px-5 py-3 font-medium">رقم القضية</th>
                    <th class="px-5 py-3 font-medium">العنوان</th>
                    <th class="px-5 py-3 font-medium">الموكل</th>
                    <th class="px-5 py-3 font-medium">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach($recentCases as $case)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-5 py-3 text-sm font-mono text-[#1E3A5F] dark:text-blue-300">{{ $case->case_number }}</td>
                    <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-200 truncate max-w-[200px]">{{ $case->getTranslation('title','ar',false) }}</td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $case->client?->getTranslation('name','ar',false) ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span @class([
                            'text-xs px-2.5 py-1 rounded-full font-medium',
                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => $case->status === 'new',
                            'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $case->status === 'active',
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' => $case->status === 'pending',
                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' => $case->status === 'closed',
                        ])>{{ $case->status_label }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
const labels = @json($monthlyRevenue->pluck('month'));
const data   = @json($monthlyRevenue->pluck('total'));
const isDark = document.documentElement.classList.contains('dark');

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'الإيرادات (ج.م)',
            data,
            backgroundColor: '#C9A84C',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: isDark ? '#374151' : '#F3F4F6' }, ticks: { color: isDark ? '#9CA3AF' : '#6B7280' } },
            x: { grid: { display: false },                        ticks: { color: isDark ? '#9CA3AF' : '#6B7280' } },
        }
    }
});
</script>
@endpush
@endsection

