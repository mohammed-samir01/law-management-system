<x-filament-panels::page>
    {{ $this->form }}

    @if($result)
    <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4">
        @php
        $cards = [
            'عدد الفواتير'        => $result['count'],
            'الإجمالي'            => $result['total'],
            'الصافي (قبل الضريبة)' => $result['net'],
            'ضريبة القيمة المضافة' => $result['vat'],
            'الضريبة المحصّلة'     => $result['vat_paid'],
        ];
        @endphp
        @foreach($cards as $label => $value)
        <div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</div>
            <div class="mt-1 text-2xl font-black text-gray-900 dark:text-white" dir="ltr">{{ $value }}</div>
        </div>
        @endforeach
    </div>

    <p class="mt-4 text-xs text-gray-400">
        ملاحظة: التقديم الإلكتروني الفعلي للفواتير إلى هيئة الضرائب (ZATCA / مصلحة الضرائب) يتطلب شهادة اعتماد
        تُضبط من مسؤول المنصة، وهو معطّل افتراضياً. هذا التقرير للاستخدام الداخلي.
    </p>
    @endif
</x-filament-panels::page>
