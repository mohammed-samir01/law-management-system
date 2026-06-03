@extends('mobile.layouts.app')

@section('title', 'تفاصيل العميل — ميزان')

@section('header')
@section('header-title', 'تفاصيل العميل')
@section('header-right')
<a href="{{ route('mobile.lawyer.clients') }}" class="text-white/80 active:text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endsection
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    {{-- Client Header --}}
    <div class="bg-[#1E3A5F] rounded-2xl p-5 text-white mb-4 text-center">
        <div class="w-16 h-16 bg-[#C9A84C] rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="text-white font-bold text-2xl">
                {{ mb_substr($client->getTranslation('name', 'ar', false), 0, 1) }}
            </span>
        </div>
        <h2 class="font-bold text-xl">{{ $client->getTranslation('name', 'ar', false) }}</h2>
        @if($client->phone)
        <p class="text-white/70 text-sm mt-1">{{ $client->phone }}</p>
        @endif
        @if($client->email)
        <p class="text-white/70 text-sm">{{ $client->email }}</p>
        @endif
    </div>

    {{-- Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-4 space-y-3">
        @if($client->national_id)
        <div class="flex justify-between">
            <span class="text-xs text-gray-400 dark:text-gray-500">الهوية الوطنية</span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $client->national_id }}</span>
        </div>
        @endif
        @if($client->client_type)
        <div class="flex justify-between">
            <span class="text-xs text-gray-400 dark:text-gray-500">نوع العميل</span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $client->client_type }}</span>
        </div>
        @endif
        <div class="flex justify-between">
            <span class="text-xs text-gray-400 dark:text-gray-500">الحالة</span>
            @if($client->is_active)
            <span class="text-xs bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 px-2 py-0.5 rounded-full">نشط</span>
            @else
            <span class="text-xs bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 px-2 py-0.5 rounded-full">غير نشط</span>
            @endif
        </div>
    </div>

    {{-- Cases --}}
    @if($client->cases->count())
    <div>
        <h3 class="font-bold text-[#1E3A5F] dark:text-white mb-2">القضايا ({{ $client->cases->count() }})</h3>
        <div class="space-y-2">
            @foreach($client->cases as $case)
            <a href="{{ route('mobile.lawyer.cases.show', $case->id) }}"
               class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl p-3.5 shadow-sm border border-gray-100 dark:border-gray-700 active:opacity-70">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">{{ $case->getTranslation('title', 'ar', false) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $case->case_number }}</p>
                </div>
                <span @class([
                    'text-xs px-2 py-0.5 rounded-full mr-2 shrink-0',
                    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $case->status === 'active',
                    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => $case->status === 'new',
                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' => in_array($case->status, ['closed', 'archived']),
                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' => $case->status === 'pending',
                ])>{{ $case->status_label }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@section('bottomnav')
@include('mobile.components.lawyer-nav')
@endsection

