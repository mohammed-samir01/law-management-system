@extends('mobile.layouts.app')

@section('title', 'تم الدفع — عامر')

@section('header')
@section('header-title', 'تم الدفع بنجاح')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-8 flex flex-col items-center">

    {{-- Success Icon --}}
    <div class="w-24 h-24 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-1">تم الدفع بنجاح</h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mb-8">تمت معالجة دفعتك بنجاح</p>

    {{-- Invoice Summary --}}
    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 mb-6 space-y-3">
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">رقم الفاتورة</span>
            <span class="text-sm font-bold text-[#1E3A5F] dark:text-blue-300">{{ $invoice->invoice_number }}</span>
        </div>
        @if($invoice->client)
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">الموكل</span>
            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $invoice->client->getTranslation('name', 'ar', false) }}</span>
        </div>
        @endif
        <div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex justify-between">
            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">المبلغ المدفوع</span>
            <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
        </div>
    </div>

    {{-- Actions --}}
    <div class="w-full space-y-3">
        <a href="{{ route('mobile.client.invoices.show', $invoice->id) }}"
           class="flex items-center justify-center w-full bg-[#1E3A5F] text-white rounded-2xl py-4 font-bold active:opacity-80">
            عرض الفاتورة
        </a>
        <a href="{{ route('mobile.client.invoices') }}"
           class="flex items-center justify-center w-full border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-2xl py-4 font-bold active:opacity-70">
            العودة للفواتير
        </a>
    </div>

</div>
@endsection
