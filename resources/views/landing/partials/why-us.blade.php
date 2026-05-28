@php
$whyUs = $settings['why_us'];

$iconMap = [
    'lightbulb' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    'lock'      => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
    'clock'     => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    'star'      => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    'shield'    => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'users'     => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
];
@endphp

<section id="why-us" class="py-24 bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="text-center mb-16 scroll-animate opacity-0">
            <span class="inline-block text-gold font-semibold text-sm uppercase tracking-widest mb-3" x-text="lang==='ar'?'ما يميزنا':'Our Advantage'"></span>
            <h2 class="text-3xl sm:text-4xl font-bold text-navy dark:text-white mb-4" style="font-family:'Playfair Display',serif;" x-text="lang==='ar'?'لماذا تختارنا؟':'Why Choose Us?'"></h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto" x-text="lang==='ar'?'نؤمن أن كل عميل يستحق أفضل تمثيل قانوني ممكن':'We believe every client deserves the best possible legal representation'"></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-{{ count($whyUs) <= 2 ? count($whyUs) : '3' }} gap-10">
            @foreach($whyUs as $i => $item)
            @php $svgPath = $iconMap[$item['icon'] ?? 'lightbulb'] ?? $iconMap['lightbulb']; @endphp
            <div class="text-center scroll-animate opacity-0" style="animation-delay:{{ $i * 0.1 }}s;">
                <div class="w-20 h-20 rounded-full bg-navy/5 dark:bg-navy/30 flex items-center justify-center mx-auto mb-6 ring-4 ring-gold/20">
                    <svg class="w-10 h-10 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svgPath }}"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-navy dark:text-white mb-3"
                    x-text="lang==='ar' ? '{{ $item['title_ar'] }}' : '{{ $item['title_en'] }}'"></h3>
                <p class="text-gray-500 dark:text-gray-400 leading-relaxed text-sm"
                    x-text="lang==='ar' ? '{{ addslashes($item['desc_ar']) }}' : '{{ addslashes($item['desc_en']) }}'">
                </p>
            </div>
            @endforeach
        </div>

        <!-- CTA Banner -->
        <div class="mt-20 relative overflow-hidden rounded-3xl scroll-animate opacity-0" style="animation-delay:0.3s;">

            <!-- Background -->
            <div class="absolute inset-0 bg-navy"></div>

            <!-- Subtle dot pattern -->
            <div class="absolute inset-0 opacity-5">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="cta-dots" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                            <circle cx="12" cy="12" r="1.5" fill="#C9A84C"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#cta-dots)"/>
                </svg>
            </div>

            <!-- Gold accent lines -->
            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-gold/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"></div>
            <div class="absolute start-0 inset-y-0 w-1 bg-gradient-to-b from-transparent via-gold to-transparent"></div>

            <!-- Decorative glow -->
            <div class="absolute -top-20 -start-20 w-64 h-64 rounded-full bg-gold/8 blur-3xl"></div>
            <div class="absolute -bottom-20 -end-20 w-64 h-64 rounded-full bg-gold/6 blur-3xl"></div>

            <!-- Content -->
            <div class="relative z-10 px-8 py-12 md:px-14 md:py-14
                        flex flex-col md:flex-row items-center justify-between gap-8">

                <!-- Left: Text -->
                <div class="text-center md:text-start max-w-xl">
                    <!-- Icon + label -->
                    <div class="inline-flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                        <span class="text-gold text-sm font-semibold uppercase tracking-widest"
                              x-text="lang==='ar'?'استشارة مجانية':'Free Consultation'"></span>
                    </div>

                    <h3 class="text-2xl sm:text-3xl font-bold text-white mb-3 leading-snug"
                        style="font-family:'Playfair Display',serif;"
                        x-text="lang==='ar'
                            ? 'هل تحتاج إلى مساعدة قانونية؟'
                            : 'Do You Need Legal Help?'"></h3>

                    <p class="text-white/55 text-sm sm:text-base leading-relaxed"
                       x-text="lang==='ar'
                           ? 'تواصل معنا اليوم واحصل على استشارة قانونية مجانية مع أحد محامينا المتخصصين'
                           : 'Contact us today and get a free legal consultation with one of our specialized lawyers'"></p>
                </div>

                <!-- Right: CTA -->
                <div class="flex flex-col sm:flex-row items-center gap-4 flex-shrink-0">
                    <a href="#contact"
                       class="group inline-flex items-center gap-3 px-8 py-4 rounded-2xl
                              bg-gold hover:bg-gold-dark text-white font-bold text-sm
                              transition-all duration-300 shadow-xl shadow-gold/25
                              hover:shadow-gold/40 hover:-translate-y-1">
                        <span x-text="lang==='ar'?'ابدأ استشارتك المجانية':'Start Free Consultation'"></span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1 rtl:rotate-180"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="tel:+96611234567"
                       class="inline-flex items-center gap-2 px-6 py-4 rounded-2xl
                              border border-white/20 hover:border-gold/60 text-white/80 hover:text-gold
                              font-medium text-sm transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span x-text="lang==='ar'?'اتصل الآن':'Call Now'"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
