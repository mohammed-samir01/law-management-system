@extends('mobile.layouts.app')

@section('title', 'الجلسات — عامر')

@section('header')
@section('header-title', 'الجلسات')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4"
     x-data="{
        aiModal: false,
        aiLoading: false,
        aiSending: false,
        aiText: '',
        aiError: '',
        hearingId: null,
        async generateReminder(id) {
            this.hearingId = id; this.aiLoading = true; this.aiText = ''; this.aiError = ''; this.aiModal = true;
            try {
                const res = await fetch(`/mobile/lawyer/hearings/${id}/ai/reminder`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.aiText = data.message || data.error || 'لا توجد نتيجة';
            } catch(e) { this.aiError = 'حدث خطأ في الاتصال'; }
            finally { this.aiLoading = false; }
        },
        async sendReminder() {
            this.aiSending = true;
            try {
                const res = await fetch(`/mobile/lawyer/hearings/${this.hearingId}/ai/send-reminder`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: this.aiText })
                });
                const data = await res.json();
                if (data.sent) { this.aiModal = false; }
                else { this.aiError = data.error || 'فشل الإرسال'; }
            } catch(e) { this.aiError = 'حدث خطأ في الاتصال'; }
            finally { this.aiSending = false; }
        }
     }">

    {{-- Toggle: Upcoming / All --}}
    <div class="flex gap-2 mb-4">
        <a href="{{ route('mobile.lawyer.hearings', ['filter' => 'upcoming']) }}"
           class="flex-1 text-center py-2 rounded-xl text-sm font-medium transition-colors
                  {{ request('filter', 'upcoming') === 'upcoming'
                      ? 'bg-[#1E3A5F] text-white'
                      : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600' }}">
            القادمة
        </a>
        <a href="{{ route('mobile.lawyer.hearings', ['filter' => 'all']) }}"
           class="flex-1 text-center py-2 rounded-xl text-sm font-medium transition-colors
                  {{ request('filter') === 'all'
                      ? 'bg-[#1E3A5F] text-white'
                      : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600' }}">
            الكل
        </a>
    </div>

    {{-- Hearings List --}}
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
                    <p class="font-bold text-[#1E3A5F] dark:text-blue-300 truncate">
                        {{ $hearing->legalCase->getTranslation('title', 'ar', false) }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $hearing->legalCase->case_number }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-sm text-gray-600 dark:text-gray-300">🕐 {{ $hearing->scheduled_at->format('H:i') }}</span>
                        @if($hearing->court)
                        <span class="text-sm text-gray-600 dark:text-gray-300 truncate">📍 {{ $hearing->court }}</span>
                        @endif
                    </div>
                </div>
                <span @class([
                    'shrink-0 text-xs px-2 py-0.5 rounded-full',
                    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $hearing->status === 'scheduled',
                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' => $hearing->status === 'completed',
                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' => $hearing->status === 'postponed',
                    'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300' => $hearing->status === 'cancelled',
                ])>{{ $hearing->status_label }}</span>
            </div>
            @if($hearing->status === 'scheduled')
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <button @click="generateReminder({{ $hearing->id }})"
                        class="flex items-center gap-1.5 text-xs text-[#C9A84C] font-medium active:opacity-70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    توليد تذكير AI للموكل
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-12 text-gray-400 dark:text-gray-600">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm">لا توجد جلسات</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
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

    {{-- AI Reminder Modal --}}
    <div x-show="aiModal" x-transition class="fixed inset-0 z-50 flex items-end" style="display:none;">
        <div class="absolute inset-0 bg-black/50" @click="aiModal = false"></div>
        <div class="relative w-full bg-white dark:bg-gray-800 rounded-t-3xl p-6 max-h-[85vh] flex flex-col">
            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-4"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-[#1E3A5F] dark:text-white text-lg">تذكير AI للموكل</h3>
                <button @click="aiModal = false" class="text-gray-400 dark:text-gray-500 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div x-show="aiLoading" class="flex flex-col items-center justify-center py-12 gap-3">
                <div class="w-10 h-10 border-4 border-[#C9A84C] border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm text-gray-400 dark:text-gray-500">جارٍ توليد الرسالة...</p>
            </div>

            <div x-show="!aiLoading" class="flex flex-col flex-1 overflow-y-auto gap-4">
                <div x-show="aiError" class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 rounded-xl p-3 text-sm" x-text="aiError"></div>

                <div x-show="aiText">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">نص الرسالة (يمكنك التعديل)</label>
                    <textarea x-model="aiText" rows="6"
                              class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-xl px-4 py-3 text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-[#C9A84C] resize-none"></textarea>
                </div>

                <div x-show="aiText" class="flex gap-3 pb-2">
                    <button @click="sendReminder()" :disabled="aiSending"
                            class="flex-1 bg-[#C9A84C] text-white rounded-xl py-3 font-bold active:opacity-80 disabled:opacity-50 flex items-center justify-center gap-2">
                        <span x-show="!aiSending">إرسال للموكل</span>
                        <span x-show="aiSending">جارٍ الإرسال...</span>
                    </button>
                    <button @click="aiModal = false"
                            class="flex-1 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-xl py-3 font-bold active:opacity-70">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('bottomnav')
@include('mobile.components.lawyer-nav')
@endsection

