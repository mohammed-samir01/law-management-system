<x-filament-widgets::widget>
    @php
        $isTrial   = $subscription?->onTrial();
        $isWarning = $daysLeft <= 7;
        $usable    = $subscription?->isUsable();
    @endphp

    <x-filament::section>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div @class([
                    'flex h-12 w-12 items-center justify-center rounded-full',
                    'bg-warning-100 text-warning-600' => $isWarning && $usable,
                    'bg-success-100 text-success-600' => ! $isWarning && $usable,
                    'bg-danger-100 text-danger-600'   => ! $usable,
                ])>
                    <x-filament::icon icon="heroicon-o-credit-card" class="h-6 w-6" />
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $isTrial ? 'تجربة مجانية' : 'اشتراكك الحالي' }}
                    </div>
                    <div class="text-lg font-bold text-gray-950 dark:text-white">
                        {{ $planName }}
                        @if($usable)
                            <span class="text-sm font-normal text-gray-500">— باقٍ {{ $daysLeft }} يوم</span>
                        @else
                            <span class="text-sm font-normal text-danger-600">— منتهٍ</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($isWarning || ! $usable)
                    <a href="{{ route('billing.show') }}">
                        <x-filament::button color="warning" icon="heroicon-o-arrow-up-circle">
                            {{ $isTrial ? 'فعّل اشتراكك' : 'جدّد / رقّ' }}
                        </x-filament::button>
                    </a>
                @else
                    <a href="{{ route('billing.show') }}">
                        <x-filament::button color="gray" outlined icon="heroicon-o-cog-6-tooth">
                            إدارة الاشتراك
                        </x-filament::button>
                    </a>
                @endif
            </div>
        </div>

        @if($isTrial)
            <div class="mt-4 rounded-lg bg-warning-50 dark:bg-warning-500/10 px-4 py-3 text-sm text-warning-700 dark:text-warning-400">
                أنت في الفترة التجريبية المجانية ({{ $daysLeft }} يوم متبقٍ). فعّل اشتراكك قبل الانتهاء لضمان استمرار الخدمة.
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
