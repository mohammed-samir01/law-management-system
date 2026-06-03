@extends('mobile.layouts.app')

@section('title', 'الدفع — ميزان')

@section('header')
@section('header-title', 'دفع الفاتورة')
@section('header-right')
<a href="{{ route('mobile.lawyer.invoices.show', $invoice->id) }}" class="text-white/80 active:text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endsection
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4" x-data="{ submitting: false }">

    {{-- Invoice Summary --}}
    <div class="bg-[#1E3A5F] rounded-2xl p-4 text-white mb-5">
        <p class="text-white/70 text-sm">الفاتورة رقم</p>
        <p class="font-bold text-lg">{{ $invoice->invoice_number }}</p>
        @if($invoice->client)
        <p class="text-white/60 text-sm mt-1">{{ $invoice->client->getTranslation('name', 'ar', false) }}</p>
        @endif
        <p class="text-3xl font-bold mt-3">{{ number_format($invoice->total_amount, 2) }} <span class="text-sm font-normal text-white/60">ج.م</span></p>
    </div>

    {{-- Card Form --}}
    <form method="POST"
          action="{{ route('mobile.invoices.pay.initiate', $invoice->id) }}"
          @submit="submitting = true">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-4 space-y-4">

            <h3 class="font-bold text-[#1E3A5F] dark:text-white text-sm">بيانات البطاقة</h3>

            {{-- Card Name --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">الاسم على البطاقة</label>
                <input type="text" name="card_name" required
                       class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                       placeholder="AHMED HASSAN">
                @error('card_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Card Number --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">رقم البطاقة</label>
                <input type="text" name="card_number" required inputmode="numeric"
                       maxlength="16" pattern="\d{16}"
                       class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 text-sm tracking-widest focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                       placeholder="1234 5678 9012 3456">
                @error('card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Row: Month / Year / CVC --}}
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">الشهر</label>
                    <input type="text" name="card_month" required inputmode="numeric"
                           maxlength="2" pattern="\d{1,2}"
                           class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-3 py-3 text-sm text-center focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="MM">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">السنة</label>
                    <input type="text" name="card_year" required inputmode="numeric"
                           maxlength="4" pattern="\d{4}"
                           class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-3 py-3 text-sm text-center focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="YYYY">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">CVC</label>
                    <input type="text" name="card_cvc" required inputmode="numeric"
                           maxlength="4" pattern="\d{3,4}"
                           class="w-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-3 py-3 text-sm text-center focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="123">
                </div>
            </div>
        </div>

        {{-- Security note --}}
        <div class="flex items-center gap-2 mb-5 text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <p class="text-xs">بياناتك محمية ومشفّرة بالكامل</p>
        </div>

        <button type="submit"
                :disabled="submitting"
                class="w-full bg-[#C9A84C] text-white rounded-2xl py-4 font-bold text-lg active:opacity-80 disabled:opacity-50 flex items-center justify-center gap-2">
            <span x-show="!submitting">ادفع {{ number_format($invoice->total_amount, 2) }} ج.م</span>
            <span x-show="submitting">جارٍ المعالجة...</span>
        </button>
    </form>

</div>
@endsection
