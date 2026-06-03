@extends('mobile.layouts.app')

@section('title', 'الفواتير — ميزان')

@section('header')
@section('header-title', 'الفواتير')
@section('header-right', '')
@section('header-left', '')
@endsection

@section('content')
<div class="px-4 pt-4">

    {{-- Status Filter --}}
    <div class="flex gap-2 overflow-x-auto pb-1 mb-4 no-scrollbar">
        @foreach([''=>'الكل','draft'=>'مسودة','sent'=>'مرسلة','paid'=>'مدفوعة','overdue'=>'متأخرة'] as $val => $label)
        <a href="{{ route('mobile.lawyer.invoices', ['status' => $val]) }}"
           class="shrink-0 px-4 py-1.5 rounded-full text-sm border transition-colors
                  {{ request('status', '') === $val
                      ? 'bg-[#1E3A5F] text-white border-[#1E3A5F]'
                      : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Invoices List --}}
    <div class="space-y-3 mb-4">
        @forelse($invoices as $invoice)
        <a href="{{ route('mobile.lawyer.invoices.show', $invoice->id) }}"
           class="block bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 active:opacity-70">
            <div class="flex items-start justify-between gap-2 mb-3">
                <div>
                    <p class="font-bold text-[#1E3A5F] dark:text-blue-300">{{ $invoice->invoice_number }}</p>
                    @if($invoice->client)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $invoice->client->getTranslation('name', 'ar', false) }}</p>
                    @endif
                </div>
                <span @class([
                    'text-xs px-2.5 py-1 rounded-full font-medium shrink-0',
                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' => $invoice->status === 'draft',
                    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' => $invoice->status === 'sent',
                    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $invoice->status === 'paid',
                    'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' => $invoice->status === 'overdue',
                    'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' => $invoice->status === 'cancelled',
                ])>{{ $invoice->status_label }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        {{ number_format($invoice->total_amount, 0) }}
                        <span class="text-sm font-normal text-gray-400 dark:text-gray-500">ج.م</span>
                    </p>
                    @if($invoice->due_date)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">استحقاق: {{ $invoice->due_date->format('d/m/Y') }}</p>
                    @endif
                </div>
                @if($invoice->legalCase)
                <p class="text-xs text-[#1E3A5F] dark:text-blue-300 bg-[#1E3A5F]/5 dark:bg-[#1E3A5F]/20 px-2 py-1 rounded-lg truncate max-w-[40%]">
                    {{ $invoice->legalCase->case_number }}
                </p>
                @endif
            </div>
        </a>
        @empty
        <div class="text-center py-12 text-gray-400 dark:text-gray-600">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">لا توجد فواتير</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div class="flex justify-between items-center py-3">
        @if($invoices->onFirstPage())
        <span class="text-gray-300 dark:text-gray-600 text-sm">السابق</span>
        @else
        <a href="{{ $invoices->previousPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">السابق</a>
        @endif
        <span class="text-gray-400 dark:text-gray-500 text-xs">{{ $invoices->currentPage() }} / {{ $invoices->lastPage() }}</span>
        @if($invoices->hasMorePages())
        <a href="{{ $invoices->nextPageUrl() }}" class="text-[#1E3A5F] dark:text-blue-300 text-sm font-medium">التالي</a>
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

