<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">تنزيل نسخة احتياطية</x-slot>
        <x-slot name="description">
            صدّر نسخة كاملة من بيانات مكتبك (القضايا، العملاء، الجلسات، الفواتير، المهام…) كملف JSON.
            البيانات مقصورة على مكتبك فقط.
        </x-slot>

        <x-filament::button wire:click="download" icon="heroicon-o-arrow-down-tray" wire:loading.attr="disabled">
            تنزيل النسخة الآن
        </x-filament::button>

        <p class="mt-4 text-xs text-gray-400">
            ملاحظة: الرفع التلقائي إلى التخزين السحابي (S3 / Google Drive) يتطلب ضبط بيانات اعتماد من مسؤول المنصة،
            وهو معطّل افتراضياً. الاستعادة من نسخة احتياطية غير متاحة من هنا حمايةً لبياناتك.
        </p>
    </x-filament::section>
</x-filament-panels::page>
