@extends('mobile.layouts.app')

@section('title', 'مستندات القضية — عامر')

@section('header')
@section('header-title', 'المستندات')
@section('header-right')
<a href="{{ route('mobile.lawyer.cases.show', $case->id) }}" class="text-white/80 active:text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endsection
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4" x-data="{
    uploading: false,
    showModal: false,
    aiModal: false,
    aiLoading: false,
    aiText: '',
    aiError: '',
    currentDocId: null,
    async analyzeDoc(docId) {
        this.currentDocId = docId; this.aiLoading = true; this.aiText = ''; this.aiError = ''; this.aiModal = true;
        try {
            const res = await fetch(`/mobile/lawyer/cases/{{ $case->id }}/documents/${docId}/ai/analyze`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.aiText = data.analysis || data.error || 'لا توجد نتيجة';
        } catch(e) { this.aiError = 'حدث خطأ في الاتصال'; }
        finally { this.aiLoading = false; }
    }
}">

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-xl px-4 py-3 mb-4 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Case title --}}
    <div class="mb-4">
        <p class="text-xs text-gray-400 dark:text-gray-500">القضية</p>
        <p class="font-bold text-[#1E3A5F] dark:text-white">{{ $case->getTranslation('title', 'ar', false) }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $case->case_number }}</p>
    </div>

    {{-- Upload button --}}
    <button @click="showModal = true"
            class="w-full flex items-center justify-center gap-2 bg-[#1E3A5F] text-white rounded-2xl py-3.5 font-bold mb-5 active:opacity-80">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        رفع مستند جديد
    </button>

    {{-- Documents list --}}
    <div class="space-y-3">
        @forelse($case->documents as $doc)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <div class="w-11 h-11 bg-[#C9A84C]/10 dark:bg-[#C9A84C]/20 rounded-xl flex items-center justify-center shrink-0">
                @php
                    $firstMedia = $doc->getFirstMedia('files');
                    $ext = $firstMedia ? pathinfo($firstMedia->file_name, PATHINFO_EXTENSION) : '';
                @endphp
                @if(in_array($ext, ['jpg','jpeg','png']))
                <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                @else
                <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                    {{ $doc->getTranslation('title', 'ar', false) ?: 'مستند' }}
                </p>
                @if($firstMedia)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    {{ strtoupper($ext) }} • {{ round($firstMedia->size / 1024) }} KB
                </p>
                @endif
            </div>
            @if($firstMedia)
            <div class="flex gap-1 shrink-0">
                <button @click="analyzeDoc({{ $doc->id }})"
                        class="text-purple-500 dark:text-purple-400 p-1" title="تحليل AI">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </button>
                <a href="{{ route('mobile.lawyer.cases.documents.view', [$case->id, $doc->id]) }}" target="_blank"
                   class="text-[#C9A84C] p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
                <a href="{{ $firstMedia->getUrl() }}" target="_blank"
                   class="text-[#1E3A5F] dark:text-blue-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </a>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-12 text-gray-400 dark:text-gray-600">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">لا توجد مستندات بعد</p>
        </div>
        @endforelse
    </div>

    {{-- AI Analysis Modal --}}
    <div x-show="aiModal" x-transition class="fixed inset-0 z-50 flex items-end" style="display:none;">
        <div class="absolute inset-0 bg-black/50" @click="aiModal = false"></div>
        <div class="relative w-full bg-white dark:bg-gray-800 rounded-t-3xl p-6 max-h-[80vh] flex flex-col">
            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-4"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-[#1E3A5F] dark:text-white text-lg">تحليل AI للمستند</h3>
                <button @click="aiModal = false" class="text-gray-400 dark:text-gray-500 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto flex-1">
                <div x-show="aiLoading" class="flex flex-col items-center justify-center py-12 gap-3">
                    <div class="w-10 h-10 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-400 dark:text-gray-500">جارٍ تحليل المستند...</p>
                </div>
                <div x-show="aiError" class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 rounded-xl p-4 text-sm" x-text="aiError"></div>
                <p x-show="aiText && !aiLoading" class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line" x-text="aiText"></p>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div x-show="showModal" x-transition
         class="fixed inset-0 z-50 flex items-end"
         style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>

        {{-- Sheet --}}
        <div class="relative w-full bg-white dark:bg-gray-800 rounded-t-3xl p-6 safe-bottom">
            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-5"></div>
            <h3 class="font-bold text-[#1E3A5F] dark:text-white text-lg mb-4">رفع مستند</h3>

            <form method="POST"
                  action="{{ route('mobile.lawyer.cases.documents.upload', $case->id) }}"
                  enctype="multipart/form-data"
                  @submit="uploading = true">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">اسم المستند (اختياري)</label>
                    <input type="text" name="title"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="مثال: عقد الاتفاق">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">الملف</label>
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer bg-gray-50 dark:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">اضغط لاختيار ملف</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF، صورة، Word (حد أقصى 20MB)</span>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden" required>
                    </label>
                    @error('file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        :disabled="uploading"
                        class="w-full bg-[#1E3A5F] text-white rounded-xl py-3.5 font-bold active:opacity-80 disabled:opacity-50 flex items-center justify-center gap-2">
                    <span x-show="!uploading">رفع المستند</span>
                    <span x-show="uploading">جاري الرفع...</span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@section('bottomnav')
@include('mobile.components.lawyer-nav')
@endsection

