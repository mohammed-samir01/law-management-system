@extends('mobile.layouts.app')

@section('title', 'جلساتي — ميزان')

@section('header')
@section('header-title', 'جلساتي القادمة')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    <div class="space-y-3 mb-4">
        @forelse($hearings as $hearing)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-start gap-3">
                <div class="w-14 h-14 bg-[#1E3A5F] rounded-xl flex flex-col items-center justify-center shrink-0 text-white">
                    <span class="font-bold text-xl leading-none">{{ $hearing->scheduled_at->format('d') }}</span>
                    <span class="text-xs opacity-70">{{ $hearing->scheduled_at->format('M') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    @if($hearing->legalCase)
                    <p class="font-bold text-[#1E3A5F] dark:text-blue-300 truncate">{{ $hearing->legalCase->getTranslation('title', 'ar', false) }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-sm text-gray-600 dark:text-gray-300">🕐 {{ $hearing->scheduled_at->format('H:i') }}</span>
                        @if($hearing->court)
                        <span class="text-sm text-gray-600 dark:text-gray-300">📍 {{ $hearing->court }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-400 dark:text-gray-600">
            <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm">لا توجد جلسات قادمة</p>
        </div>
        @endforelse
    </div>

    @if($hearings->hasPages())
    <div class="flex justify-between items-center py-3">
        @if($hearings->onFirstPage())
        <span class="text-gray-300 dark:text-gray-600 text-sm">السابق</span>
        @else
        <a href="{{ $hearings->previousPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">السابق</a>
        @endif
        <span class="text-gray-400 dark:text-gray-500 text-xs">{{ $hearings->currentPage() }} / {{ $hearings->lastPage() }}</span>
        @if($hearings->hasMorePages())
        <a href="{{ $hearings->nextPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">التالي</a>
        @else
        <span class="text-gray-300 dark:text-gray-600 text-sm">التالي</span>
        @endif
    </div>
    @endif

</div>
@endsection

@section('bottomnav')
@include('mobile.components.client-nav')
@endsection

