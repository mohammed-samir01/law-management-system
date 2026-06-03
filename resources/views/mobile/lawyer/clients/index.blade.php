@extends('mobile.layouts.app')

@section('title', 'العملاء — ميزان')

@section('header')
@section('header-title', 'العملاء')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    {{-- Search --}}
    <form method="GET" action="{{ route('mobile.lawyer.clients') }}" class="mb-4">
        <div class="relative">
            <input type="search" name="search" value="{{ request('search') }}"
                   placeholder="بحث بالاسم أو الهاتف..."
                   class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 pr-10 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </form>

    {{-- Clients List --}}
    <div class="space-y-3 mb-4">
        @forelse($clients as $client)
        <a href="{{ route('mobile.lawyer.clients.show', $client->id) }}"
           class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 active:opacity-70">
            <div class="w-12 h-12 bg-[#1E3A5F] rounded-full flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-lg">
                    {{ mb_substr($client->getTranslation('name', 'ar', false), 0, 1) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-800 dark:text-gray-100 truncate">{{ $client->getTranslation('name', 'ar', false) }}</p>
                @if($client->phone)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $client->phone }}</p>
                @endif
                @if($client->cases_count)
                <p class="text-xs text-[#1E3A5F] dark:text-blue-300 mt-0.5">{{ $client->cases_count }} قضية</p>
                @endif
            </div>
            <div class="shrink-0">
                @if($client->is_active)
                <span class="text-xs bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 px-2 py-0.5 rounded-full">نشط</span>
                @else
                <span class="text-xs bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 px-2 py-0.5 rounded-full">غير نشط</span>
                @endif
            </div>
        </a>
        @empty
        <div class="text-center py-12 text-gray-400 dark:text-gray-600">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm">لا يوجد عملاء</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($clients->hasPages())
    <div class="flex justify-between items-center py-3">
        @if($clients->onFirstPage())
        <span class="text-gray-300 dark:text-gray-600 text-sm">السابق</span>
        @else
        <a href="{{ $clients->previousPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">السابق</a>
        @endif
        <span class="text-gray-400 dark:text-gray-500 text-xs">{{ $clients->currentPage() }} / {{ $clients->lastPage() }}</span>
        @if($clients->hasMorePages())
        <a href="{{ $clients->nextPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">التالي</a>
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

