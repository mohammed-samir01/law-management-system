<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">رابط الاشتراك في التقويم (ICS)</x-slot>
        <x-slot name="description">
            انسخ الرابط وأضِفه في Google Calendar (إضافة تقويم → من رابط URL) أو تطبيق التقويم على هاتفك.
            سيُحدَّث تلقائياً عند تغيّر جلساتك.
        </x-slot>

        <div class="flex items-center gap-2" dir="ltr">
            <input type="text" readonly value="{{ $feedUrl }}"
                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm font-mono"
                   onclick="this.select()">
            <a href="{{ $feedUrl }}" target="_blank"
               class="shrink-0 rounded-lg bg-primary-600 text-white text-sm font-semibold px-4 py-2 hover:bg-primary-500">
               فتح
            </a>
        </div>

        <p class="mt-3 text-xs text-gray-400">
            الرابط سرّي وخاص بمكتبك — لا تشاركه إلا مع من يحتاج الاطّلاع على التقويم.
        </p>
    </x-filament::section>
</x-filament-panels::page>
