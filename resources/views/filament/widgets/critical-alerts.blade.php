<x-filament-widgets::widget>
    @if(count($items))
    <x-filament::section>
        <x-slot name="heading">
            <span class="text-danger-600 dark:text-danger-400 font-bold">⚠️ تنبيهات حرجة</span>
        </x-slot>

        <div class="space-y-2">
            @foreach($items as $item)
            <div class="flex items-center justify-between gap-3 rounded-lg px-3 py-2
                        {{ $item['severity'] === 'high'
                            ? 'bg-danger-50 dark:bg-danger-500/10'
                            : 'bg-warning-50 dark:bg-warning-500/10' }}">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2 h-2 rounded-full shrink-0 {{ $item['severity'] === 'high' ? 'bg-danger-500' : 'bg-warning-500' }}"></span>
                    @if($item['url'])
                    <a href="{{ $item['url'] }}" class="text-sm font-medium text-gray-900 dark:text-white truncate hover:underline">{{ $item['title'] }}</a>
                    @else
                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['title'] }}</span>
                    @endif
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0" dir="ltr">
                    {{ $item['due_date']?->format('Y/m/d') ?? '' }}
                </span>
            </div>
            @endforeach
        </div>
    </x-filament::section>
    @endif
</x-filament-widgets::widget>
