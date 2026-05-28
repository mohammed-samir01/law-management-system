@extends('mobile.layouts.app')

@section('title', 'تفاصيل القضية — عامر')

@section('header')
@section('header-title', 'تفاصيل القضية')
@section('header-right')
<a href="{{ route('mobile.client.cases') }}" class="text-white/80 active:text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endsection
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    <div class="bg-[#1E3A5F] rounded-2xl p-5 text-white mb-4">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h2 class="font-bold text-lg leading-snug flex-1">{{ $case->getTranslation('title', 'ar', false) }}</h2>
            <span @class([
                'shrink-0 text-xs px-2.5 py-1 rounded-full',
                'bg-green-400/30 text-green-200' => $case->status === 'active',
                'bg-blue-400/30 text-blue-200' => $case->status === 'new',
                'bg-yellow-400/30 text-yellow-200' => $case->status === 'pending',
                'bg-white/20 text-white/70' => in_array($case->status, ['closed','archived']),
            ])>{{ $case->status_label }}</span>
        </div>
        <p class="text-white/60 text-sm">{{ $case->case_number }}</p>
        @if($case->court) <p class="text-white/70 text-sm mt-1">📍 {{ $case->court }}</p> @endif
    </div>

    @if($case->getTranslation('description', 'ar', false))
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-3">
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-2 font-medium">تفاصيل القضية</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->getTranslation('description', 'ar', false) }}</p>
    </div>
    @endif

    {{-- Hearings --}}
    @if($case->hearings->count())
    <div class="mb-3">
        <h3 class="font-bold text-[#1E3A5F] dark:text-white mb-2">الجلسات</h3>
        <div class="space-y-2">
            @foreach($case->hearings as $hearing)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-3.5 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-10 h-10 bg-[#1E3A5F]/10 dark:bg-[#1E3A5F]/30 rounded-xl flex flex-col items-center justify-center shrink-0">
                    <span class="text-[#1E3A5F] dark:text-blue-300 font-bold text-xs">{{ $hearing->scheduled_at->format('d') }}</span>
                    <span class="text-[#1E3A5F] dark:text-blue-300 text-[10px]">{{ $hearing->scheduled_at->format('M') }}</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ $hearing->scheduled_at->format('H:i') }}
                        @if($hearing->court) — {{ $hearing->court }} @endif
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $hearing->status_label }}</p>
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

