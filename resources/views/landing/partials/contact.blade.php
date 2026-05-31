@php
$contact = $settings['contact'];
$socials = [
    'facebook'  => ['icon' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z', 'url' => $contact['facebook'] ?? null],
    'twitter_x' => ['icon' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z', 'url' => $contact['twitter_x'] ?? null],
    'instagram' => ['icon' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z', 'url' => $contact['instagram'] ?? null],
    'linkedin'  => ['icon' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z', 'url' => $contact['linkedin'] ?? null],
    'youtube'   => ['icon' => 'M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z', 'url' => $contact['youtube'] ?? null],
    'tiktok'    => ['icon' => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z', 'url' => $contact['tiktok'] ?? null],
];
$activeSocials = array_filter($socials, fn($s) => !empty($s['url']));
@endphp

<section id="contact" class="py-24 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="text-center mb-16 scroll-animate opacity-0">
            <span class="inline-block text-gold font-semibold text-sm uppercase tracking-widest mb-3" x-text="lang==='ar'?'نحن هنا':'We Are Here'"></span>
            <h2 class="text-3xl sm:text-4xl font-bold text-navy dark:text-white mb-4" style="font-family:'Playfair Display',serif;" x-text="lang==='ar'?'تواصل معنا':'Get in Touch'"></h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto" x-text="lang==='ar'?'نوفر استشارة قانونية أولية مجانية. تواصل معنا اليوم':'We offer a free initial legal consultation. Contact us today'"></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <!-- Contact info -->
            <div class="space-y-8 scroll-animate opacity-0">
                <div>
                    <h3 class="text-xl font-bold text-navy dark:text-white mb-6" x-text="lang==='ar'?'معلومات التواصل':'Contact Information'"></h3>

                    <div class="space-y-5">
                        @if(!empty($contact['address_ar']))
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-navy/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-navy dark:text-white text-sm" x-text="lang==='ar'?'العنوان':'Address'"></div>
                                <div class="text-gray-500 dark:text-gray-400 text-sm mt-1"
                                    x-text="lang==='ar' ? '{{ addslashes($contact['address_ar']) }}' : '{{ addslashes($contact['address_en'] ?? $contact['address_ar']) }}'">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($contact['phone']))
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-navy/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-navy dark:text-white text-sm" x-text="lang==='ar'?'الهاتف':'Phone'"></div>
                                <a href="tel:{{ $contact['phone'] }}" class="text-gray-500 dark:text-gray-400 text-sm mt-1 block hover:text-gold transition-colors" dir="ltr">{{ $contact['phone'] }}</a>
                                @if(!empty($contact['phone2']))
                                <a href="tel:{{ $contact['phone2'] }}" class="text-gray-500 dark:text-gray-400 text-sm block hover:text-gold transition-colors" dir="ltr">{{ $contact['phone2'] }}</a>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(!empty($contact['email']))
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-navy/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-navy dark:text-white text-sm" x-text="lang==='ar'?'البريد الإلكتروني':'Email'"></div>
                                <a href="mailto:{{ $contact['email'] }}" class="text-gray-500 dark:text-gray-400 text-sm mt-1 block hover:text-gold transition-colors" dir="ltr">{{ $contact['email'] }}</a>
                            </div>
                        </div>
                        @endif

                        @if(!empty($contact['working_hours_ar']))
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-navy/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-navy dark:text-white text-sm" x-text="lang==='ar'?'ساعات العمل':'Working Hours'"></div>
                                <div class="text-gray-500 dark:text-gray-400 text-sm mt-1"
                                    x-text="lang==='ar' ? '{{ addslashes($contact['working_hours_ar']) }}' : '{{ addslashes($contact['working_hours_en'] ?? $contact['working_hours_ar']) }}'">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($contact['whatsapp']))
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-navy/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-navy dark:text-white text-sm">WhatsApp</div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['whatsapp']) }}" target="_blank" class="text-gray-500 dark:text-gray-400 text-sm mt-1 block hover:text-gold transition-colors" dir="ltr">{{ $contact['whatsapp'] }}</a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Social media -->
                    @if(count($activeSocials) > 0)
                    <div class="mt-8">
                        <div class="font-semibold text-navy dark:text-white text-sm mb-3" x-text="lang==='ar'?'تابعنا على':'Follow Us'"></div>
                        <div class="flex gap-3 flex-wrap">
                            @foreach($activeSocials as $platform => $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                               class="w-9 h-9 rounded-full bg-navy/5 dark:bg-navy/30 hover:bg-gold hover:text-white flex items-center justify-center transition-colors text-navy dark:text-gray-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="{{ $social['icon'] }}"/>
                                </svg>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Interactive Map — OpenStreetMap (no API key required) -->
                <div class="rounded-2xl overflow-hidden border border-navy/10 dark:border-navy/30 h-56">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox=31.4721%2C30.5577%2C31.5321%2C30.6177&layer=mapnik&marker=30.5877%2C31.5021"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="موقع مكتب عامر للمحاماة — الزقازيق"
                    ></iframe>
                </div>
            </div>

            <!-- Contact Form -->
            <div
                x-data="{
                    form: { name: '', email: '', phone: '', subject: '', message: '', office_slug: '{{ $officeSlug ?? '' }}' },
                    loading: false,
                    success: false,
                    error: '',
                    async submit() {
                        this.loading = true;
                        this.error = '';
                        try {
                            const res = await fetch('{{ route('contact') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(this.form)
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.success = true;
                                this.form = { name: '', email: '', phone: '', subject: '', message: '', office_slug: '{{ $officeSlug ?? '' }}' };
                            } else {
                                this.error = data.message || (lang === 'ar' ? 'حدث خطأ، حاول مرة أخرى' : 'An error occurred, please try again');
                            }
                        } catch(e) {
                            this.error = lang === 'ar' ? 'فشل الاتصال بالخادم' : 'Connection failed, please try again';
                        }
                        this.loading = false;
                    }
                }"
                class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm scroll-animate opacity-0"
                style="animation-delay:0.1s;"
            >
                <!-- Success state -->
                <div x-show="success" x-cloak class="text-center py-10">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy dark:text-white mb-2" x-text="lang==='ar'?'تم الإرسال بنجاح!':'Message Sent Successfully!'"></h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6" x-text="lang==='ar'?'سيتواصل معك فريقنا في أقرب وقت ممكن':'Our team will contact you as soon as possible'"></p>
                    <button @click="success=false" class="text-gold hover:underline text-sm" x-text="lang==='ar'?'إرسال رسالة أخرى':'Send Another Message'"></button>
                </div>

                <!-- Form -->
                <form x-show="!success" @submit.prevent="submit()">
                    <h3 class="text-xl font-bold text-navy dark:text-white mb-6" x-text="lang==='ar'?'أرسل رسالتك':'Send Your Message'"></h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" x-text="lang==='ar'?'الاسم الكامل *':'Full Name *'"></label>
                            <input x-model="form.name" type="text" required :placeholder="lang==='ar'?'اسمك الكامل':'Your full name'" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-gold transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" x-text="lang==='ar'?'البريد الإلكتروني *':'Email *'"></label>
                            <input x-model="form.email" type="email" required :placeholder="lang==='ar'?'بريدك الإلكتروني':'your@email.com'" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-gold transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" x-text="lang==='ar'?'رقم الهاتف':'Phone Number'"></label>
                            <input x-model="form.phone" type="tel" :placeholder="lang==='ar'?'رقم هاتفك':'+966 5x xxx xxxx'" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-gold transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" x-text="lang==='ar'?'موضوع الاستفسار *':'Subject *'"></label>
                            <input x-model="form.subject" type="text" required :placeholder="lang==='ar'?'موضوع رسالتك':'Message subject'" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-gold transition">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" x-text="lang==='ar'?'تفاصيل الاستفسار *':'Message Details *'"></label>
                        <textarea x-model="form.message" required rows="4" :placeholder="lang==='ar'?'اكتب تفاصيل استفساركم أو قضيتكم هنا...':'Write your inquiry or case details here...'" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-gold transition resize-none"></textarea>
                    </div>

                    <div x-show="error" x-cloak class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-sm" x-text="error"></div>

                    <button type="submit" :disabled="loading" class="w-full py-3.5 rounded-xl bg-navy hover:bg-navy-light text-white font-semibold text-sm transition-all duration-300 disabled:opacity-60 flex items-center justify-center gap-2">
                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? (lang==='ar'?'جاري الإرسال...':'Sending...') : (lang==='ar'?'إرسال الرسالة':'Send Message')"></span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>
