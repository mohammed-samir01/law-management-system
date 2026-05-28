<nav class="fixed bottom-0 inset-x-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-50 safe-bottom">
    <div class="flex justify-around items-center h-16">
        {{-- Dashboard --}}
        <a href="{{ route('mobile.client.dashboard') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->routeIs('mobile.client.dashboard') ? 'text-[#1E3A5F]' : 'text-gray-400 dark:text-gray-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs">الرئيسية</span>
        </a>
        {{-- Cases --}}
        <a href="{{ route('mobile.client.cases') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->routeIs('mobile.client.cases*') ? 'text-[#1E3A5F]' : 'text-gray-400 dark:text-gray-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="text-xs">قضاياي</span>
        </a>
        {{-- Hearings --}}
        <a href="{{ route('mobile.client.hearings') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->routeIs('mobile.client.hearings*') ? 'text-[#1E3A5F]' : 'text-gray-400 dark:text-gray-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs">جلساتي</span>
        </a>
        {{-- Invoices --}}
        <a href="{{ route('mobile.client.invoices') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->routeIs('mobile.client.invoices*') ? 'text-[#1E3A5F]' : 'text-gray-400 dark:text-gray-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-xs">فواتيري</span>
        </a>
    </div>
</nav>
