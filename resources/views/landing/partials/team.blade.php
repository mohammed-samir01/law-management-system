@php $team = $settings['team']; @endphp

<section id="team" class="py-24 bg-white dark:bg-gray-950 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="text-center mb-16 scroll-animate opacity-0">
            <span class="inline-flex items-center gap-2 text-gold font-semibold text-sm uppercase tracking-widest mb-4">
                <span class="w-8 h-px bg-gold inline-block"></span>
                <span x-text="lang==='ar'?'خبراؤنا':'Our Experts'"></span>
                <span class="w-8 h-px bg-gold inline-block"></span>
            </span>
            <h2 class="text-3xl sm:text-4xl font-bold text-navy dark:text-white mb-4"
                style="font-family:'Playfair Display',serif;"
                x-text="lang==='ar'?'فريقنا القانوني':'Our Legal Team'"></h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto leading-relaxed"
               x-text="lang==='ar'?'نخبة من المحامين المتخصصين بخبرات واسعة في مختلف تخصصات القانون':'An elite team of specialized lawyers with extensive expertise across all areas of law'"></p>
        </div>

        <!-- Team grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">

            @foreach($team as $i => $member)
            @php
                $photoPath = $member['photo'] ?? null;
                $photoUrl  = $photoPath
                    ? (str_starts_with($photoPath, 'http') ? $photoPath : asset('storage/' . $photoPath))
                    : null;
                $colors = ['bg-navy', 'bg-gold', 'bg-navy-light'];
                $bgColor = $member['color'] ?? $colors[$i % count($colors)];
            @endphp

            <div class="group w-full scroll-animate opacity-0 flex flex-col"
                 style="animation-delay:{{ $i * 0.12 }}s;">

                <div class="relative flex flex-col flex-1 bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-md
                            hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100 dark:border-gray-700">

                    <!-- Photo area -->
                    <div class="relative h-72 overflow-hidden {{ $bgColor }} flex items-end justify-center">

                        <!-- Background pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="dots-{{ $i }}" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                        <circle cx="10" cy="10" r="1.5" fill="white"/>
                                    </pattern>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#dots-{{ $i }})"/>
                            </svg>
                        </div>

                        <!-- Gradient overlay at bottom -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>

                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $member['name_ar'] ?? '' }}"
                                 class="absolute inset-0 w-full h-full object-cover object-top
                                        group-hover:scale-105 transition-transform duration-700">
                        @else
                            <!-- Initials placeholder — elegant -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-28 h-28 rounded-full bg-white/15 backdrop-blur-sm border-2 border-white/30
                                            flex items-center justify-center shadow-xl">
                                    <span class="text-white font-bold text-5xl" style="font-family:'Tajawal',sans-serif;">
                                        {{ $member['initials'] }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Gold accent bar at bottom of photo -->
                        <div class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-gold to-transparent"></div>
                    </div>

                    <!-- Info area -->
                    <div class="p-6 flex flex-col flex-1">

                        <!-- Name -->
                        <h3 class="text-lg font-bold text-navy dark:text-white mb-1 leading-snug"
                            x-text="lang==='ar' ? '{{ $member['name_ar'] }}' : '{{ $member['name_en'] }}'"></h3>

                        <!-- Role tag -->
                        <div class="inline-flex items-center gap-1.5 mb-4">
                            <span class="w-2 h-2 rounded-full bg-gold flex-shrink-0"></span>
                            <span class="text-gold text-sm font-medium leading-tight"
                                  x-text="lang==='ar' ? '{{ $member['role_ar'] }}' : '{{ $member['role_en'] }}'"></span>
                        </div>

                        <!-- Divider -->
                        <div class="w-10 h-px bg-gray-200 dark:bg-gray-600 mb-4"></div>

                        <!-- Bio -->
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed line-clamp-3 flex-1"
                           x-text="lang==='ar' ? '{{ addslashes($member['bio_ar']) }}' : '{{ addslashes($member['bio_en']) }}'"></p>

                        <!-- Contact actions -->
                        <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <a href="#contact"
                               class="inline-flex items-center gap-2 text-xs font-semibold text-navy dark:text-gray-300
                                      hover:text-gold dark:hover:text-gold transition-colors"
                               x-text="lang==='ar'?'تواصل معنا':'Get in touch'"></a>
                            <div class="flex gap-2">
                                <a href="#contact"
                                   class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gold hover:text-white
                                          flex items-center justify-center text-gray-500 dark:text-gray-400
                                          transition-all duration-200 group/btn">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                                <a href="#contact"
                                   class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gold hover:text-white
                                          flex items-center justify-center text-gray-500 dark:text-gray-400
                                          transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>
