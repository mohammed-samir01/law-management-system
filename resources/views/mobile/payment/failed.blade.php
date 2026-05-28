@extends('mobile.layouts.app')

@section('title', 'فشل الدفع — عامر')

@section('header')
@section('header-title', 'فشل الدفع')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-8 flex flex-col items-center">

    {{-- Failed Icon --}}
    <div class="w-24 h-24 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-1">فشلت عملية الدفع</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 text-center">{{ $error }}</p>

    {{-- Invoice Info --}}
    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
        <div class="flex justify-between mb-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">رقم الفاتورة</span>
            <span class="text-sm font-bold text-[#1E3A5F] dark:text-blue-300">{{ $invoice->invoice_number }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">المبلغ</span>
            <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
        </div>
    </div>

    {{-- Actions --}}
    <div class="w-full space-y-3">
        <a href="{{ route('mobile.invoices.pay.show', $invoice->id) }}"
           class="flex items-center justify-center w-full bg-[#C9A84C] text-white rounded-2xl py-4 font-bold active:opacity-80">
            إعادة المحاولة
        </a>
        <a href="{{ route('mobile.client.invoices') }}"
           class="flex items-center justify-center w-full border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-2xl py-4 font-bold active:opacity-70">
            العودة للفواتير
        </a>
    </div>

</div>
@endsection
