@extends('desktop.layouts.app')
@section('title', 'الجلسات — ميزان')
@section('page-title', 'تقويم الجلسات')

@section('content')
<div class="grid grid-cols-3 gap-5">

    {{-- Calendar Grid --}}
    <div class="col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700"
         x-data="{
            month: {{ $month }},
            year:  {{ $year }},
            nav(dir) {
                let d = new Date(this.year, this.month - 1 + dir, 1);
                this.month = d.getMonth() + 1;
                this.year  = d.getFullYear();
                window.location = `/desktop/calendar?month=${this.month}&year=${this.year}`;
            }
         }">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <button @click="nav(-1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <h3 class="font-bold text-gray-700 dark:text-gray-200">
                {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('MMMM Y') }}
            </h3>
            <button @click="nav(1)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        @php
            $firstDay   = \Carbon\Carbon::create($year, $month, 1);
            $daysInMonth = $firstDay->daysInMonth;
            $startDow   = ($firstDay->dayOfWeek + 1) % 7; // Sat=0
            $dayNames   = ['سبت','أحد','إثنين','ثلاثاء','أربعاء','خميس','جمعة'];
        @endphp

        <div class="p-4">
            {{-- Day Headers --}}
            <div class="grid grid-cols-7 mb-2">
                @foreach($dayNames as $d)
                <div class="text-center text-xs font-medium text-gray-400 dark:text-gray-500 py-1">{{ $d }}</div>
                @endforeach
            </div>

            {{-- Days Grid --}}
            <div class="grid grid-cols-7 gap-1">
                @for($i = 0; $i < $startDow; $i++)
                <div></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey  = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $dayHrgs  = $hearings[$dateKey] ?? collect();
                    $isToday  = $dateKey === now()->format('Y-m-d');
                @endphp
                <div class="min-h-[64px] rounded-xl p-1.5 border {{ $isToday ? 'border-[#C9A84C] bg-[#C9A84C]/5' : 'border-gray-100 dark:border-gray-700' }}">
                    <p class="text-xs font-medium {{ $isToday ? 'text-[#C9A84C]' : 'text-gray-600 dark:text-gray-300' }} mb-1">{{ $day }}</p>
                    @foreach($dayHrgs->take(2) as $h)
                    <div class="bg-[#1E3A5F] text-white text-[10px] rounded px-1 py-0.5 mb-0.5 truncate">
                        {{ $h->scheduled_at->format('H:i') }}
                    </div>
                    @endforeach
                    @if($dayHrgs->count() > 2)
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">+{{ $dayHrgs->count() - 2 }} أخرى</p>
                    @endif
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Upcoming List --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200">القادمة</h3>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-700">
            @forelse($upcoming as $h)
            <div class="px-4 py-3 flex items-start gap-3">
                <div class="w-10 h-10 bg-[#1E3A5F] rounded-xl flex flex-col items-center justify-center shrink-0 text-white">
                    <span class="font-bold text-sm leading-none">{{ $h->scheduled_at->format('d') }}</span>
                    <span class="text-[10px] opacity-70">{{ $h->scheduled_at->format('M') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">{{ $h->legalCase?->getTranslation('title','ar',false) ?? '—' }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $h->scheduled_at->format('H:i') }}@if($h->court) · {{ $h->court }}@endif</p>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">لا توجد جلسات قادمة</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

