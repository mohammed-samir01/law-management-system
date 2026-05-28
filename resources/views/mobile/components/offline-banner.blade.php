{{-- Offline indicator — uses navigator.onLine + browser events --}}
<div
    x-data="{
        online: navigator.onLine,
        init() {
            window.addEventListener('online',  () => { this.online = true });
            window.addEventListener('offline', () => { this.online = false });
        }
    }"
    x-show="!online"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="fixed top-0 inset-x-0 z-50 bg-red-600 text-white text-center text-sm font-medium py-2 px-4 safe-top"
    style="display: none;"
>
    <div class="flex items-center justify-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M3 3l18 18"/>
        </svg>
        <span>لا يوجد اتصال بالإنترنت</span>
    </div>
</div>
