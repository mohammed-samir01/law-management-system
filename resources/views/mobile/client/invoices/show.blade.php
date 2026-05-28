@extends('mobile.layouts.app')

@section('title', 'تفاصيل الفاتورة — عامر')

@section('header')
@section('header-title', 'تفاصيل الفاتورة')
@section('header-right')
<a href="{{ route('mobile.client.invoices') }}" class="text-white/80 active:text-white">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endsection
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    {{-- Invoice Header --}}
    <div class="bg-[#1E3A5F] rounded-2xl p-5 text-white mb-4">
        <div class="flex items-start justify-between gap-2 mb-3">
            <p class="font-bold text-xl">{{ $invoice->invoice_number }}</p>
            <span @class([
                'text-xs px-3 py-1.5 rounded-full font-medium shrink-0',
                'bg-blue-400/30 text-blue-200'   => $invoice->status === 'sent',
                'bg-green-400/30 text-green-200' => $invoice->status === 'paid',
                'bg-red-400/30 text-red-200'     => $invoice->status === 'overdue',
                'bg-white/20 text-white/60'      => in_array($invoice->status, ['draft','cancelled']),
            ])>{{ $invoice->status_label }}</span>
        </div>
        <p class="text-3xl font-bold">{{ number_format($invoice->total_amount, 2) }} <span class="text-base font-normal text-white/60">ج.م</span></p>
        @if($invoice->due_date)
        <p class="text-white/60 text-sm mt-2">تاريخ الاستحقاق: {{ $invoice->due_date->format('d/m/Y') }}</p>
        @endif
    </div>

    {{-- Amounts --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-3 space-y-2.5">
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">المبلغ الأساسي</span>
            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ number_format($invoice->amount, 2) }} ج.م</span>
        </div>
        @if($invoice->tax_amount > 0)
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">الضريبة</span>
            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ number_format($invoice->tax_amount, 2) }} ج.م</span>
        </div>
        @endif
        <div class="border-t border-gray-100 dark:border-gray-700 pt-2 flex justify-between">
            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">الإجمالي</span>
            <span class="text-sm font-bold text-[#1E3A5F] dark:text-blue-300">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
        </div>
    </div>

    {{-- Case --}}
    @if($invoice->legalCase)
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 mb-3">
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">القضية</p>
        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $invoice->legalCase->getTranslation('title', 'ar', false) }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->legalCase->case_number }}</p>
    </div>
    @endif

    {{-- Actions --}}
    <div class="space-y-3 pb-4">
        <a href="{{ route('mobile.invoices.pdf', $invoice->id) }}" target="_blank"
           class="flex items-center justify-center gap-2 w-full border-2 border-[#1E3A5F] dark:border-blue-400 text-[#1E3A5F] dark:text-blue-300 rounded-2xl py-3.5 font-bold active:opacity-70">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            عرض PDF
        </a>

        @if(in_array($invoice->status, ['sent', 'overdue']))
        <a href="{{ route('mobile.invoices.pay.show', $invoice->id) }}"
           class="flex items-center justify-center gap-2 w-full bg-[#C9A84C] text-white rounded-2xl py-3.5 font-bold active:opacity-80">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            ادفع الآن
        </a>
        @endif
    </div>

</div>
@endsection

@section('bottomnav')
@include('mobile.components.client-nav')
@endsection

