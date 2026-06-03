@extends('desktop.layouts.app')
@section('title', 'تفاصيل القضية — ميزان')
@section('page-title', $case->case_number . ' — ' . $case->getTranslation('title','ar',false))

@section('content')
<div class="grid grid-cols-3 gap-5">

    {{-- Main Info --}}
    <div class="col-span-2 space-y-5">

        <div class="bg-[#1E3A5F] rounded-2xl p-5 text-white">
            <div class="flex items-start justify-between mb-3">
                <h2 class="text-xl font-bold">{{ $case->getTranslation('title','ar',false) }}</h2>
                <span @class([
                    'text-xs px-3 py-1.5 rounded-full font-medium shrink-0',
                    'bg-blue-400/30 text-blue-200'   => $case->status === 'new',
                    'bg-green-400/30 text-green-200' => $case->status === 'active',
                    'bg-yellow-400/30 text-yellow-200' => $case->status === 'pending',
                    'bg-white/20 text-white/60'      => $case->status === 'closed',
                ])>{{ $case->status_label }}</span>
            </div>
            <p class="text-white/60 text-sm">{{ $case->case_number }}@if($case->court) — {{ $case->court }}@endif</p>
            @if($case->getTranslation('description','ar',false))
            <p class="text-white/80 text-sm mt-3 leading-relaxed">{{ $case->getTranslation('description','ar',false) }}</p>
            @endif
        </div>

        {{-- Hearings Table --}}
        @if($case->hearings->count())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">الجلسات ({{ $case->hearings->count() }})</h3>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="text-right text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-5 py-3 font-medium">التاريخ</th>
                        <th class="px-5 py-3 font-medium">المحكمة</th>
                        <th class="px-5 py-3 font-medium">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($case->hearings as $hearing)
                    <tr>
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $hearing->scheduled_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $hearing->court ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span @class([
                                'text-xs px-2 py-0.5 rounded-full',
                                'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $hearing->status === 'scheduled',
                                'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'        => $hearing->status === 'completed',
                            ])>{{ $hearing->status_label }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Documents --}}
        @if($case->documents->count())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">المستندات ({{ $case->documents->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach($case->documents as $doc)
                <div class="px-5 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $doc->getTranslation('title','ar',false) ?: 'مستند' }}</span>
                    @if($doc->getFirstMedia('files'))
                    <a href="{{ route('desktop.documents.view', $doc->id) }}" target="_blank"
                       class="text-xs text-[#C9A84C] hover:underline">عرض</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <a href="{{ route('desktop.cases') }}"
           class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-[#1E3A5F] dark:hover:text-white mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للقضايا
        </a>

        @if($case->client)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3 font-medium">الموكل</p>
            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $case->client->getTranslation('name','ar',false) }}</p>
            @if($case->client->phone)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $case->client->phone }}</p>
            @endif
            @if($case->client->email)
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $case->client->email }}</p>
            @endif
        </div>
        @endif

        @if($case->invoices->count())
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3 font-medium">الفواتير</p>
            <div class="space-y-2">
                @foreach($case->invoices as $inv)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $inv->invoice_number }}</span>
                    <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ number_format($inv->total_amount, 0) }} ج.م</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

