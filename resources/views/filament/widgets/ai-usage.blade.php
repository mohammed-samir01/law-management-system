<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 mb-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 text-primary-600">
                    <x-filament::icon icon="heroicon-o-sparkles" class="h-5 w-5" />
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">استخدام الذكاء الاصطناعي هذه الفترة</div>
                    <div class="text-lg font-bold text-gray-950 dark:text-white">
                        @if($unlimited)
                            {{ $used }} طلب <span class="text-sm font-normal text-gray-500">(غير محدود)</span>
                        @else
                            {{ $used }} / {{ $limit }} طلب
                        @endif
                    </div>
                </div>
            </div>

            @if(! $unlimited)
                <a href="{{ route('billing.show') }}">
                    <x-filament::button color="gray" outlined size="sm">ترقية الخطة</x-filament::button>
                </a>
            @endif
        </div>

        @unless($unlimited)
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                <div @class([
                    'h-2.5 rounded-full transition-all',
                    'bg-primary-500' => $percent < 80,
                    'bg-warning-500' => $percent >= 80 && $percent < 100,
                    'bg-danger-500'  => $percent >= 100,
                ]) style="width: {{ $percent }}%"></div>
            </div>
            @if($percent >= 80)
                <p class="mt-2 text-sm {{ $percent >= 100 ? 'text-danger-600' : 'text-warning-600' }}">
                    {{ $percent >= 100 ? 'بلغت الحد الأقصى لهذه الفترة.' : 'اقتربت من بلوغ الحد الأقصى.' }}
                </p>
            @endif
        @endunless
    </x-filament::section>
</x-filament-widgets::widget>
