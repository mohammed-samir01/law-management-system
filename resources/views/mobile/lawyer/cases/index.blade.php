@extends('mobile.layouts.app')

@section('title', 'القضايا — عامر')

@section('header')
@section('header-title', 'القضايا')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('mobile.lawyer.cases') }}" class="mb-4 space-y-2">
        <div class="relative">
            <input type="search" name="search" value="{{ request('search') }}"
                   placeholder="بحث برقم القضية أو العنوان..."
                   class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 pr-10 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
            @foreach([''=>'الكل','new'=>'جديدة','active'=>'نشطة','pending'=>'معلقة','closed'=>'مغلقة'] as $val => $label)
            <button type="submit" name="status" value="{{ $val }}"
                    class="shrink-0 px-4 py-1.5 rounded-full text-sm border transition-colors
                           {{ request('status', '') === $val
                               ? 'bg-[#1E3A5F] text-white border-[#1E3A5F]'
                               : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </form>

    {{-- Cases List --}}
    <div class="space-y-3 mb-4">
        @forelse($cases as $case)
        <a href="{{ route('mobile.lawyer.cases.show', $case->id) }}"
           class="block bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 active:opacity-70">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-[#1E3A5F] dark:text-blue-300 truncate">{{ $case->getTranslation('title', 'ar', false) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $case->case_number }}</p>
                    @if($case->client)
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                        <span class="text-gray-400 dark:text-gray-500">العميل: </span>
                        {{ $case->client->getTranslation('name', 'ar', false) }}
                    </p>
                    @endif
                </div>
                <span @class([
                    'shrink-0 text-xs px-2.5 py-1 rounded-full font-medium',
                    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => $case->status === 'new',
                    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $case->status === 'active',
                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' => $case->status === 'pending',
                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' => $case->status === 'closed',
                    'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' => $case->status === 'archived',
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

    {{-- Pagination --}}
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
@include('mobile.components.lawyer-nav')
@endsection

