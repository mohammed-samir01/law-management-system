<x-filament-panels::page>

    {{ $this->form }}

    @if($report)
    <div class="mt-8 fi-section rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $report['title'] }}</h3>
        </div>

        @if(count($report['rows']))
        <table class="w-full text-sm" dir="rtl">
            <tbody>
                @foreach($report['rows'] as $row)
                <tr class="border-b border-gray-50 dark:border-gray-800/50">
                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $row['label'] }}</td>
                    <td class="px-6 py-3 text-end font-bold text-gray-900 dark:text-white" dir="ltr">{{ $row['value'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="px-6 py-8 text-center text-sm text-gray-400">لا توجد بيانات ضمن النطاق المحدد.</p>
        @endif
    </div>
    @endif

</x-filament-panels::page>
