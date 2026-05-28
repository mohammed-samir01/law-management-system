@extends('desktop.layouts.app')
@section('title', 'الموكلون — عامر')
@section('page-title', 'الموكلون')

@section('search')
<form method="GET" action="{{ route('desktop.clients') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث باسم أو هاتف..."
           class="border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg px-3 py-1.5 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
</form>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <table class="w-full">
        <thead>
            <tr class="text-right text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                <th class="px-5 py-3.5 font-medium">الاسم</th>
                <th class="px-5 py-3.5 font-medium">الهاتف</th>
                <th class="px-5 py-3.5 font-medium">البريد</th>
                <th class="px-5 py-3.5 font-medium">القضايا</th>
                <th class="px-5 py-3.5 font-medium">الفواتير</th>
                <th class="px-5 py-3.5 font-medium">تاريخ الانضمام</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
            @forelse($clients as $client)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-[#1E3A5F]/10 dark:bg-[#1E3A5F]/30 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-[#1E3A5F] dark:text-blue-300 font-bold text-xs">{{ mb_substr($client->getTranslation('name','ar',false), 0, 1) }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $client->getTranslation('name','ar',false) }}</span>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $client->phone ?? '—' }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $client->email ?? '—' }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $client->legal_cases_count ?? 0 }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $client->invoices_count ?? 0 }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-400 dark:text-gray-500">{{ $client->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">لا يوجد موكلون</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($clients->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <p class="text-sm text-gray-400 dark:text-gray-500">{{ $clients->total() }} موكل</p>
        <div class="flex gap-2">
            @if(!$clients->onFirstPage())
            <a href="{{ $clients->previousPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">السابق</a>
            @endif
            @if($clients->hasMorePages())
            <a href="{{ $clients->nextPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">التالي</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

