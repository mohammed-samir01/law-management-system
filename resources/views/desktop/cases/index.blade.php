@extends('desktop.layouts.app')
@section('title', 'القضايا — عامر')
@section('page-title', 'القضايا')

@section('search')
<form method="GET" action="{{ route('desktop.cases') }}" class="flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..."
           class="border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg px-3 py-1.5 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
    <select name="status"
            class="border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
            onchange="this.form.submit()">
        <option value="">كل الحالات</option>
        <option value="new"     {{ request('status') === 'new'     ? 'selected' : '' }}>جديدة</option>
        <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>نشطة</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>معلقة</option>
        <option value="closed"  {{ request('status') === 'closed'  ? 'selected' : '' }}>مغلقة</option>
    </select>
</form>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <table class="w-full">
        <thead>
            <tr class="text-right text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                <th class="px-5 py-3.5 font-medium">رقم القضية</th>
                <th class="px-5 py-3.5 font-medium">العنوان</th>
                <th class="px-5 py-3.5 font-medium">الموكل</th>
                <th class="px-5 py-3.5 font-medium">الجلسات</th>
                <th class="px-5 py-3.5 font-medium">الحالة</th>
                <th class="px-5 py-3.5 font-medium">تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
            @forelse($cases as $case)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                onclick="window.location='{{ route('desktop.cases.show', $case->id) }}'">
                <td class="px-5 py-3.5 text-sm font-mono text-[#1E3A5F] dark:text-blue-300">{{ $case->case_number }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-700 dark:text-gray-200 max-w-[220px] truncate">{{ $case->getTranslation('title','ar',false) }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $case->client?->getTranslation('name','ar',false) ?? '—' }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">{{ $case->hearings_count ?? $case->hearings->count() }}</td>
                <td class="px-5 py-3.5">
                    <span @class([
                        'text-xs px-2.5 py-1 rounded-full font-medium',
                        'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'     => $case->status === 'new',
                        'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' => $case->status === 'active',
                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' => $case->status === 'pending',
                        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'        => $case->status === 'closed',
                    ])>{{ $case->status_label }}</span>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-400 dark:text-gray-500">{{ $case->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">لا توجد قضايا</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($cases->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <p class="text-sm text-gray-400 dark:text-gray-500">{{ $cases->total() }} قضية</p>
        <div class="flex gap-2">
            @if(!$cases->onFirstPage())
            <a href="{{ $cases->previousPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">السابق</a>
            @endif
            @if($cases->hasMorePages())
            <a href="{{ $cases->nextPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">التالي</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

