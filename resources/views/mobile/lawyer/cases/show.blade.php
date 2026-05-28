@extends('mobile.layouts.app')

@section('title', 'تفاصيل القضية — عامر')

@section('header')
@section('header-title', 'تفاصيل القضية')
@section('header-right')
<a href="{{ route('mobile.lawyer.cases') }}" class="text-white/80 active:text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endsection
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4"
     x-data="{
        aiModal: false,
        aiLoading: false,
        aiText: '',
        aiError: '',
        async fetchSummary() {
            this.aiLoading = true; this.aiText = ''; this.aiError = ''; this.aiModal = true;
            try {
                const res = await fetch('{{ route('mobile.lawyer.cases.ai.summary', $case->id) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.aiText = data.summary || data.error || 'لا توجد نتيجة';
            } catch(e) { this.aiError = 'حدث خطأ في الاتصال'; }
            finally { this.aiLoading = false; }
        }
     }">

    {{-- Case Header Card --}}
    <div class="bg-[#1E3A5F] rounded-2xl p-5 text-white mb-4">
        <div class="flex items-start justify-between gap-2 mb-3">
            <h2 class="font-bold text-lg leading-snug flex-1">{{ $case->getTranslation('title', 'ar', false) }}</h2>
            <span @class([
                'shrink-0 text-xs px-2.5 py-1 rounded-full font-medium',
                'bg-blue-400/30 text-blue-200' => $case->status === 'new',
                'bg-green-400/30 text-green-200' => $case->status === 'active',
                'bg-yellow-400/30 text-yellow-200' => $case->status === 'pending',
                'bg-white/20 text-white/70' => $case->status === 'closed',
            ])>{{ $case->status_label }}</span>
        </div>
        <p class="text-white/60 text-sm">{{ $case->case_number }}</p>
        @if($case->court)
        <p class="text-white/70 text-sm mt-1">📍 {{ $case->court }}</p>
        @endif
    </div>

    {{-- Client --}}
    @if($case->client)
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-3">
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-2 font-medium">العميل</p>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#1E3A5F]/10 dark:bg-[#1E3A5F]/30 rounded-full flex items-center justify-center">
                <span class="text-[#1E3A5F] dark:text-blue-300 font-bold text-sm">{{ mb_substr($case->client->getTranslation('name','ar',false), 0, 1) }}</span>
            </div>
            <div>
                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $case->client->getTranslation('name', 'ar', false) }}</p>
                @if($case->client->phone)
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $case->client->phone }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Description --}}
    @if($case->getTranslation('description', 'ar', false))
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-3">
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-2 font-medium">الوصف</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->getTranslation('description', 'ar', false) }}</p>
    </div>
    @endif

    {{-- Hearings --}}
    @if($case->hearings->count())
    <div class="mb-3">
        <h3 class="font-bold text-[#1E3A5F] dark:text-white mb-2">الجلسات ({{ $case->hearings->count() }})</h3>
        <div class="space-y-2">
            @foreach($case->hearings as $hearing)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-3.5 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-10 h-10 bg-[#1E3A5F]/10 dark:bg-[#1E3A5F]/30 rounded-xl flex flex-col items-center justify-center shrink-0">
                    <span class="text-[#1E3A5F] dark:text-blue-300 font-bold text-xs leading-none">{{ $hearing->scheduled_at->format('d') }}</span>
                    <span class="text-[#1E3A5F] dark:text-blue-300 text-[10px]">{{ $hearing->scheduled_at->format('M') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $hearing->scheduled_at->format('H:i') }}
                        @if($hearing->court) — {{ $hearing->court }} @endif
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $hearing->status_label }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- AI Summary Button --}}
    <button @click="fetchSummary()"
            class="w-full flex items-center justify-center gap-2 bg-[#C9A84C] text-white rounded-2xl py-3.5 font-bold mb-4 active:opacity-80">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        ملخص AI للقضية
    </button>

    {{-- AI Modal --}}
    <div x-show="aiModal" x-transition class="fixed inset-0 z-50 flex items-end" style="display:none;">
        <div class="absolute inset-0 bg-black/50" @click="aiModal = false"></div>
        <div class="relative w-full bg-white dark:bg-gray-800 rounded-t-3xl p-6 max-h-[80vh] flex flex-col">
            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-4"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-[#1E3A5F] dark:text-white text-lg">ملخص AI للقضية</h3>
                <button @click="aiModal = false" class="text-gray-400 dark:text-gray-500 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto flex-1">
                <div x-show="aiLoading" class="flex flex-col items-center justify-center py-12 gap-3">
                    <div class="w-10 h-10 border-4 border-[#C9A84C] border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-400 dark:text-gray-500">جارٍ التحليل بالذكاء الاصطناعي...</p>
                </div>
                <div x-show="aiError" class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 rounded-xl p-4 text-sm" x-text="aiError"></div>
                <p x-show="aiText && !aiLoading" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line" x-text="aiText"></p>
            </div>
        </div>
    </div>

    {{-- Documents --}}
    @if($case->documents->count())
    <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-bold text-[#1E3A5F] dark:text-white">المستندات ({{ $case->documents->count() }})</h3>
            <a href="{{ route('mobile.lawyer.cases.documents', $case->id) }}" class="text-[#C9A84C] text-sm">إدارة</a>
        </div>
        <div class="space-y-2">
            @foreach($case->documents as $doc)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-9 h-9 bg-[#C9A84C]/10 dark:bg-[#C9A84C]/20 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-200 flex-1 truncate">{{ $doc->getTranslation('title', 'ar', false) ?: 'مستند' }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@section('bottomnav')
@include('mobile.components.lawyer-nav')
@endsection

