<div class="space-y-4">
    @if($template->placeholders)
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3">
        <p class="text-xs font-semibold text-amber-700 dark:text-amber-400 mb-2">المتغيرات المستخدمة في هذا القالب:</p>
        <div class="flex flex-wrap gap-2">
            @foreach($template->placeholders as $ph)
            <span class="inline-flex items-center gap-1 text-xs bg-amber-100 dark:bg-amber-800 text-amber-800 dark:text-amber-200 px-2 py-1 rounded-full font-mono">
                <span class="opacity-60">&#123;&#123;</span>{{ $ph['key'] ?? '' }}<span class="opacity-60">&#125;&#125;</span>
                <span class="text-amber-600 dark:text-amber-400">→ {{ $ph['label'] ?? '' }}</span>
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 prose prose-sm dark:prose-invert max-w-none min-h-[200px] whitespace-pre-wrap font-mono text-sm leading-relaxed text-gray-700 dark:text-gray-300" dir="rtl">
        {{ $template->content }}
    </div>
</div>
