@php
$services = $settings['services'];

$iconMap = [
    'scale'     => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
    'building'  => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    'users'     => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    'shield'    => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'briefcase' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'home'      => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'star'      => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    'lock'      => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
    'clock'     => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    'document'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'globe'     => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'money'     => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'lightbulb' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
];
@endphp

<section id="services" class="py-24 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="text-center mb-16 scroll-animate opacity-0">
            <span class="inline-block text-gold font-semibold text-sm uppercase tracking-widest mb-3" x-text="lang==='ar'?'ما نقدمه':'What We Offer'"></span>
            <h2 class="text-3xl sm:text-4xl font-bold text-navy dark:text-white mb-4" style="font-family:'Playfair Display',serif;" x-text="lang==='ar'?'خدماتنا القانونية':'Our Legal Services'"></h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto" x-text="lang==='ar'?'نقدم طيفاً واسعاً من الخدمات القانونية المتخصصة لأفراد الأعمال والشركات':'We provide a wide range of specialized legal services for individuals, businesses, and corporations'"></p>
        </div>

        <!-- Services grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $i => $service)
            @php $svgPath = $iconMap[$service['icon'] ?? 'scale'] ?? $iconMap['scale']; @endphp
            <div
                class="group bg-white dark:bg-gray-800 rounded-2xl p-7 shadow-sm hover:shadow-xl border border-transparent hover:border-gold/40 transition-all duration-300 hover:-translate-y-1 scroll-animate opacity-0"
                style="animation-delay: {{ $i * 0.08 }}s;"
            >
                <div class="w-14 h-14 rounded-xl bg-navy/5 dark:bg-navy/30 group-hover:bg-gold/10 flex items-center justify-center mb-5 transition-colors duration-300">
                    <svg class="w-7 h-7 text-navy dark:text-gray-300 group-hover:text-gold transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svgPath }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy dark:text-white mb-3 group-hover:text-gold transition-colors duration-300"
                    x-text="lang==='ar' ? '{{ $service['title_ar'] }}' : '{{ $service['title_en'] }}'">
                </h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed"
                    x-text="lang==='ar' ? '{{ addslashes($service['desc_ar']) }}' : '{{ addslashes($service['desc_en']) }}'">
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
