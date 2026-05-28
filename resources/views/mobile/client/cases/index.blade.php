@extends('mobile.layouts.app')

@section('title', 'قضاياي — عامر')

@section('header')
@section('header-title', 'قضاياي')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    <div class="space-y-3 mb-4">
        @forelse($cases as $case)
        <a href="{{ route('mobile.client.cases.show', $case->id) }}"
           class="block bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 active:opacity-70">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-[#1E3A5F] dark:text-blue-300 truncate">{{ $case->getTranslation('title', 'ar', false) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $case->case_number }}</p>
                    @if($case->court)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">📍 {{ $case->court }}</p>
                    @endif
                </div>
                <span @class([
                    'shrink-0 text-xs px-2.5 py-1 rounded-full font-medium',
                    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => $case->status === 'new',
                    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $case->status === 'active',
                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' => $case->status === 'pending',
                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' => in_array($case->status, ['closed','archived']),
                ])>{{ $case->status_label }}</span>
            </div>
        </a>
        @empty
        <div class="text-center py-12 text-gray-400 dark:text-gray-600">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm">لا توجد قضايا</p>
        </div>
        @endforelse
    </div>

    @if($cases->hasPages())
    <div class="flex justify-between items-center py-3">
        @if($cases->onFirstPage())
        <span class="text-gray-300 dark:text-gray-600 text-sm">السابق</span>
        @else
        <a href="{{ $cases->previousPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">السابق</a>
        @endif
        <span class="text-gray-400 dark:text-gray-500 text-xs">{{ $cases->currentPage() }} / {{ $cases->lastPage() }}</span>
        @if($cases->hasMorePages())
        <a href="{{ $cases->nextPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">التالي</a>
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

