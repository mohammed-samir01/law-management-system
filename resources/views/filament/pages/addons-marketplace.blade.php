<x-filament-panels::page>

@php
$categoryColors = [
    'communication' => ['bg' => '#EFF6FF', 'icon' => '#2563EB'],
    'legal'         => ['bg' => '#F0FDF4', 'icon' => '#16A34A'],
    'ai'            => ['bg' => '#FAF5FF', 'icon' => '#9333EA'],
    'client'        => ['bg' => '#FFF7ED', 'icon' => '#EA580C'],
    'analytics'     => ['bg' => '#FFFBEB', 'icon' => '#D97706'],
    'general'       => ['bg' => '#F8FAFC', 'icon' => '#475569'],
];
@endphp

<div dir="rtl" style="display:flex; flex-direction:column; gap:2.5rem;">

    {{-- Flash --}}
    @if(session('success'))
    <div class="text-green-800" style="display:flex; align-items:center; gap:.5rem; background:#F0FDF4; border:1px solid #BBF7D0; border-radius:.75rem; padding:.75rem 1rem; font-size:.875rem;">
        <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header / billing toggle --}}
    <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem;">
        <p class="text-gray-500 dark:text-gray-400" style="font-size:.875rem; margin:0;">
            وسّع إمكانيات مكتبك — فعّل ما تحتاجه وألغِ في أي وقت.
        </p>

        <div class="bg-gray-100 dark:bg-gray-800" style="display:inline-flex; align-items:center; gap:.25rem; border-radius:.75rem; padding:.25rem; font-size:.75rem; font-weight:600;">
            <button wire:click="$set('billingCycle', 'monthly')"
                @class([
                    'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' => $billingCycle === 'monthly',
                    'text-gray-500' => $billingCycle !== 'monthly',
                ])
                style="padding:.5rem 1rem; border-radius:.5rem; transition:all .2s;">
                شهري
            </button>
            <button wire:click="$set('billingCycle', 'yearly')"
                @class([
                    'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' => $billingCycle === 'yearly',
                    'text-gray-500' => $billingCycle !== 'yearly',
                ])
                style="display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1rem; border-radius:.5rem; transition:all .2s;">
                سنوي
                <span style="background:#DCFCE7; color:#15803D; font-size:10px; font-weight:700; padding:.1rem .375rem; border-radius:9999px;">وفّر 15%</span>
            </button>
        </div>
    </div>

    {{-- Categories --}}
    @foreach($this->getAddons() as $group)
    @php $color = $categoryColors[$group['key']] ?? $categoryColors['general']; @endphp

    <section style="display:flex; flex-direction:column; gap:1rem;">

        {{-- Category header --}}
        <div style="display:flex; align-items:center; gap:.625rem;">
            <span style="width:.5rem; height:.5rem; border-radius:9999px; background:{{ $color['icon'] }};"></span>
            <h2 class="text-gray-900 dark:text-white" style="font-size:.875rem; font-weight:700; margin:0;">{{ $group['label'] }}</h2>
            <span class="text-gray-400 bg-gray-100 dark:bg-gray-800" style="font-size:11px; font-weight:500; border-radius:9999px; padding:.1rem .5rem;">
                {{ count($group['addons']) }}
            </span>
        </div>

        {{-- Responsive auto-fill grid --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(290px, 1fr)); gap:1.25rem;">

            @foreach($group['addons'] as $addon)
            <div class="bg-white dark:bg-gray-900"
                 style="display:flex; flex-direction:column; border-radius:1rem; padding:1.25rem; transition:all .2s;
                        border:1px solid {{ $addon['is_active'] ? '#86EFAC' : '#E5E7EB' }};
                        {{ $addon['is_active'] ? 'box-shadow:0 0 0 1px #BBF7D0;' : '' }}"
                 onmouseover="this.style.boxShadow='0 10px 20px -5px rgba(0,0,0,.1)'; this.style.transform='translateY(-2px)';"
                 onmouseout="this.style.boxShadow='{{ $addon['is_active'] ? '0 0 0 1px #BBF7D0' : 'none' }}'; this.style.transform='none';">

                {{-- Header: icon + name --}}
                <div style="display:flex; align-items:flex-start; gap:.875rem;">
                    <div style="width:3rem; height:3rem; flex-shrink:0; border-radius:.75rem; display:flex; align-items:center; justify-content:center; background:{{ $color['bg'] }};">
                        <x-dynamic-component :component="$addon['icon']" style="width:1.5rem; height:1.5rem; color: {{ $color['icon'] }};" />
                    </div>

                    <div style="flex:1; min-width:0;">
                        <h3 class="text-gray-900 dark:text-white" style="font-size:.875rem; font-weight:700; line-height:1.25; margin:0;">{{ $addon['name_ar'] }}</h3>
                        @if($addon['is_active'])
                        <span style="display:inline-flex; align-items:center; gap:.25rem; margin-top:.25rem; font-size:11px; font-weight:700; color:#16A34A;">
                            <svg style="width:.875rem; height:.875rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            مفعّلة
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                <p class="text-gray-500 dark:text-gray-400"
                   style="margin:.75rem 0 0; font-size:.75rem; line-height:1.6; flex:1; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                    {{ $addon['desc_ar'] }}
                </p>

                {{-- Footer --}}
                <div class="border-gray-100 dark:border-gray-800" style="margin-top:1rem; padding-top:1rem; border-top:1px solid #F3F4F6;">
                    <div style="display:flex; align-items:baseline; gap:.375rem; margin-bottom:.75rem;">
                        <span class="text-gray-900 dark:text-white" style="font-size:1.5rem; font-weight:900; line-height:1;">
                            {{ $billingCycle === 'yearly'
                                ? number_format($addon['price_yearly'])
                                : number_format($addon['price_monthly']) }}
                        </span>
                        <span style="font-size:.75rem; color:#9CA3AF; font-weight:600;">{{ $addon['currency'] }}</span>
                        <span style="font-size:11px; color:#9CA3AF; font-weight:500;">
                            / {{ $billingCycle === 'yearly' ? 'سنوياً' : 'شهرياً' }}
                        </span>
                    </div>

                    @if($addon['is_active'] && $addon['expires_at'])
                    <p style="font-size:11px; color:#9CA3AF; margin:0 0 .75rem;">يتجدد في {{ $addon['expires_at'] }}</p>
                    @endif

                    @if($addon['is_active'])
                    <button
                        wire:click="cancelAddon({{ $addon['office_addon_id'] }})"
                        wire:confirm="هل تريد إلغاء الاشتراك في هذه الإضافة؟"
                        class="text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800"
                        style="width:100%; padding:.625rem; border-radius:.75rem; font-size:.75rem; font-weight:600; border:1px solid #E5E7EB; background:transparent; cursor:pointer; transition:background .2s;">
                        إلغاء الاشتراك
                    </button>
                    @else
                    <button
                        wire:click="activateAddon({{ $addon['id'] }})"
                        wire:loading.attr="disabled"
                        wire:target="activateAddon({{ $addon['id'] }})"
                        style="width:100%; padding:.625rem; border-radius:.75rem; font-size:.75rem; font-weight:700; color:#fff; border:none; cursor:pointer; transition:opacity .2s; box-shadow:0 1px 2px rgba(0,0,0,.05); background:{{ $color['icon'] }};">
                        <span wire:loading.remove wire:target="activateAddon({{ $addon['id'] }})">فعّل الآن</span>
                        <span wire:loading wire:target="activateAddon({{ $addon['id'] }})">جاري التحميل…</span>
                    </button>
                    @endif
                </div>

            </div>
            @endforeach

        </div>
    </section>
    @endforeach

</div>
</x-filament-panels::page>
