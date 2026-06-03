@extends('desktop.layouts.app')
@section('title', 'المستندات — ميزان')
@section('page-title', 'المستندات')

@section('search')
<form method="GET" action="{{ route('desktop.documents') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في المستندات..."
           class="border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg px-3 py-1.5 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
</form>
@endsection

@section('content')
<div class="grid grid-cols-3 gap-5">

    {{-- Documents Table --}}
    <div class="col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <table class="w-full">
            <thead>
                <tr class="text-right text-xs text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                    <th class="px-5 py-3.5 font-medium">العنوان</th>
                    <th class="px-5 py-3.5 font-medium">القضية</th>
                    <th class="px-5 py-3.5 font-medium">النوع</th>
                    <th class="px-5 py-3.5 font-medium">الحجم</th>
                    <th class="px-5 py-3.5 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @forelse($documents as $doc)
                @php $media = $doc->getFirstMedia('files'); @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-[#C9A84C]/10 dark:bg-[#C9A84C]/20 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $doc->getTranslation('title','ar',false) ?: 'مستند' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400 truncate max-w-[150px]">{{ $doc->legalCase?->case_number ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-xs uppercase text-gray-400 dark:text-gray-500">{{ $media ? pathinfo($media->file_name, PATHINFO_EXTENSION) : '—' }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-400 dark:text-gray-500">{{ $media ? round($media->size / 1024) . ' KB' : '—' }}</td>
                    <td class="px-5 py-3.5">
                        @if($media)
                        <a href="{{ route('desktop.documents.view', $doc->id) }}" target="_blank"
                           class="text-xs text-[#C9A84C] hover:underline">عرض</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">لا توجد مستندات</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($documents->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ $documents->total() }} مستند</p>
            <div class="flex gap-2">
                @if(!$documents->onFirstPage())
                <a href="{{ $documents->previousPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">السابق</a>
                @endif
                @if($documents->hasMorePages())
                <a href="{{ $documents->nextPageUrl() }}" class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">التالي</a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Preview Panel --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-200">معاينة</h3>
        </div>
        <div class="flex-1 flex items-center justify-center p-6 text-gray-300 dark:text-gray-600">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">اضغط "عرض" لفتح المستند</p>
            </div>
        </div>
    </div>

</div>
@endsection

