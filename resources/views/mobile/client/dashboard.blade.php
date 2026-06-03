@extends('mobile.layouts.app')

@section('title', 'الرئيسية — ميزان')

@section('header')
@section('header-title', 'ميزان')
@section('header-left')
<a href="{{ route('mobile.logout') }}"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
   class="text-white/70 active:text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
    </svg>
</a>
<form id="logout-form" method="POST" action="{{ route('mobile.logout') }}" class="hidden">@csrf</form>
@endsection
@section('header-right')
<div class="w-8 h-8 bg-[#C9A84C] rounded-full flex items-center justify-center text-white text-sm font-bold">
    {{ mb_substr($client->getTranslation('name', 'ar', false), 0, 1) }}
</div>
@endsection
@endsection

@section('content')
<div class="px-4 pt-4">

    {{-- Greeting --}}
    <div class="mb-4">
        <p class="text-gray-500 dark:text-gray-400 text-sm">مرحباً،</p>
        <h2 class="text-xl font-bold text-[#1E3A5F] dark:text-white">{{ $client->getTranslation('name', 'ar', false) }}</h2>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-[#1E3A5F] rounded-2xl p-4 text-white">
            <p class="text-3xl font-bold">{{ $stats['cases_total'] }}</p>
            <p class="text-xs text-white/70 mt-1">إجمالي قضاياي</p>
        </div>
        <div class="bg-[#C9A84C] rounded-2xl p-4 text-white">
            <p class="text-3xl font-bold">{{ $stats['cases_active'] }}</p>
            <p class="text-xs text-white/70 mt-1">قضايا نشطة</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-3xl font-bold text-[#1E3A5F] dark:text-blue-300">{{ $stats['hearings_upcoming'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">جلسات قادمة</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-3xl font-bold text-orange-500">{{ $stats['invoices_unpaid'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">فواتير غير مسددة</p>
        </div>
    </div>

    {{-- Upcoming Hearings --}}
    @if($upcomingHearings->count())
    <div class="mb-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-[#1E3A5F] dark:text-white">جلساتي القادمة</h3>
            <a href="{{ route('mobile.client.hearings') }}" class="text-[#C9A84C] text-sm">عرض الكل</a>
        </div>
        <div class="space-y-2">
            @foreach($upcomingHearings as $hearing)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-3.5 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-12 h-12 bg-[#1E3A5F]/10 dark:bg-[#1E3A5F]/30 rounded-xl flex flex-col items-center justify-center shrink-0">
                    <span class="text-[#1E3A5F] dark:text-blue-300 font-bold text-sm">{{ $hearing->scheduled_at->format('d') }}</span>
                    <span class="text-[#1E3A5F] dark:text-blue-300 text-xs">{{ $hearing->scheduled_at->format('M') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">
                        {{ $hearing->legalCase?->getTranslation('title', 'ar', false) ?? 'قضية' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $hearing->scheduled_at->format('H:i') }} • {{ $hearing->court }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@section('bottomnav')
@include('mobile.components.client-nav')
@endsection

