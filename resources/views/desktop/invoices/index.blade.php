@extends('desktop.layouts.app')
@section('title', 'الفواتير — عامر')
@section('page-title', 'الفواتير')

@section('search')
<form method="GET" action="{{ route('desktop.invoices') }}" class="flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الفاتورة..."
           class="border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg px-3 py-1.5 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
    <select name="status"
            class="border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
            onchange="this.form.submit()">
        <option value="">كل الحالات</option>
        <option value="draft"   {{ request('status') === 'draft'   ? 'selected' : '' }}>مسودة</option>
        <option value="sent"    {{ request('status') === 'sent'    ? 'selected' : '' }}>مرسلة</option>
        <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>مدفوعة</option>
        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>متأخرة</option>
    </select>
</form>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Totals --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl p-4 border border-green-100 dark:border-green-800 flex items-center gap-4">
            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-green-700 dark:text-green-300">{{ number_format($totals['paid'], 0) }} ج.م</p>
                <p class="text-sm text-green-600 dark:text-green-400">محصّل</p>
            </div>
        </div>
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-4 border border-amber-100 dark:border-amber-800 flex items-center gap-4">
            <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-amber-700 dark:text-amber-300">{{ number_format($totals['pending'], 0) }} ج.م</p>
                <p class="text-sm text-amber-600 dark:text-amber-400">معلّق</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <table class="w-full">
            <thead>
                <tr class="text-right text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                    <th class="px-5 py-3.5 font-medium">رقم الفاتورة</th>
                    <th class="px-5 py-3.5 font-medium">الموكل</th>
                    <th class="px-5 py-3.5 font-medium">القضية</th>
                    <th class="px-5 py-3.5 font-medium">المبلغ</th>
                    <th class="px-5 py-3.5 font-medium">الحالة</th>
                    <th class="px-5 py-3.5 font-medium">الاستحقاق</th>
                    <th class="px-5 py-3.5 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-5 py-3.5 text-sm font-mono text-[#1E3A5F] dark:text-blue-300">{{ $invoice->invoice_number }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-700 dark:text-gray-200">{{ $invoice->client?->getTranslation('name','ar',false) ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $invoice->legalCase?->case_number ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-sm font-bold text-gray-800 dark:text-gray-100">{{ number_format($invoice->total_amount, 2) }} ج.م</td>
                    <td class="px-5 py-3.5">
                        <span @class([
                            'text-xs px-2.5 py-1 rounded-full font-medium',
                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'        => $invoice->status === 'draft',
                            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'     => $invoice->status === 'sent',
                            'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $invoice->status === 'paid',
                            'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'         => $invoice->status === 'overdue',
                        ])>{{ $invoice->status_label }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-400 dark:text-gray-500">{{ $invoice->due_date?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('desktop.invoices.pdf', $invoice->id) }}" target="_blank"
                           class="text-xs text-[#C9A84C] hover:underline">PDF</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">لا توجد فواتير</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($invoices->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ $invoices->total() }} فاتورة</p>
            <div class="flex gap-2">
                @if(!$invoices->onFirstPage())
                <a href="{{ $invoices->previousPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">السابق</a>
                @endif
                @if($invoices->hasMorePages())
                <a href="{{ $invoices->nextPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">التالي</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

