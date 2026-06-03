@php $stats = $settings['stats']; @endphp

<section id="stats" class="py-24 bg-navy-dark dark:bg-gray-900 relative overflow-hidden">

    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5" aria-hidden="true">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="stats-geo" x="0" y="0" width="60" height="60" patternUnits="userSpaceOnUse">
                    <rect x="5" y="5" width="50" height="50" fill="none" stroke="#C9A84C" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#stats-geo)"/>
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="text-center mb-16 scroll-animate opacity-0">
            <span class="inline-block text-gold font-semibold text-sm uppercase tracking-widest mb-3" x-text="lang==='ar'?'أرقامنا تتحدث':'Numbers Speak'"></span>
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4" style="font-family:'Playfair Display',serif;" x-text="lang==='ar'?'إنجازاتنا بالأرقام':'Our Achievements'"></h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($stats as $i => $stat)
            <div class="text-center scroll-animate opacity-0" style="animation-delay:{{ $i * 0.1 }}s;">
                <div class="text-5xl font-bold text-gold mb-2" dir="ltr" style="unicode-bidi:isolate;">
                    <span data-counter="{{ $stat['value'] }}">0</span>{{ $stat['suffix'] ?? '+' }}
                </div>
                <div class="text-gray-300 text-sm"
                    x-text="lang==='ar' ? '{{ $stat['label_ar'] }}' : '{{ $stat['label_en'] }}'">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
