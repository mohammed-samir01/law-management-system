@extends('layouts.saas')

@section('content')
@php
    $hero     = $platform['hero'] ?? [];
    $stats    = $platform['stats'] ?? [];
    $features = $platform['features'] ?? [];
    $whyUs    = $platform['why_us'] ?? [];
    $contact  = $platform['contact'] ?? [];

    $featureIcons = [
        'scale'    => 'M12 3v18M5 8l-3 5h6L5 8zm14 0l-3 5h6l-3-5zM5 21h14',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'users'    => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2a4 4 0 10-4-4 4 4 0 004 4z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'cash'     => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'sparkles' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    ];
    $whyIcons = [
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
        'headset' => 'M18.364 5.636a9 9 0 010 12.728M5.636 18.364a9 9 0 010-12.728m12.728 0A9 9 0 0112 21a9 9 0 01-6.364-2.636M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'refresh' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        'star'    => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        'heart'   => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    ];
@endphp

<!-- Hero -->
<section id="hero" class="relative bg-navy text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10" aria-hidden="true">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs><pattern id="geo" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                <polygon points="40,5 75,25 75,55 40,75 5,55 5,25" fill="none" stroke="#C9A84C" stroke-width="0.5"/>
            </pattern></defs>
            <rect width="100%" height="100%" fill="url(#geo)"/>
        </svg>
    </div>
    <div class="absolute top-24 end-10 w-64 h-64 rounded-full border border-gold/20 animate-pulse" aria-hidden="true"></div>
    <div class="absolute bottom-10 start-10 w-40 h-40 rounded-full border border-gold/10" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 text-center">
        <span class="inline-block bg-gold/15 text-gold text-sm font-medium px-4 py-1.5 rounded-full mb-6" x-text="lang==='ar'?'🎉 جرّب مجاناً لمدة شهر كامل':'🎉 Try free for a full month'"></span>
        <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight mb-6" x-text="lang==='ar'?'{{ addslashes($hero['heading_ar'] ?? '') }}':'{{ addslashes($hero['heading_en'] ?? '') }}'"></h1>
        <p class="text-lg text-gray-200 max-w-3xl mx-auto mb-10" x-text="lang==='ar'?'{{ addslashes($hero['subtitle_ar'] ?? '') }}':'{{ addslashes($hero['subtitle_en'] ?? '') }}'"></p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('register.plans') }}" class="px-8 py-3 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-lg transition-colors shadow-lg" x-text="lang==='ar'?'ابدأ مجاناً — شهر كامل':'Start Free — Full Month'"></a>
            <a href="{{ route('pricing') }}" class="px-8 py-3 rounded-xl border border-white/30 hover:border-gold hover:text-gold text-white font-medium text-lg transition-colors" x-text="lang==='ar'?'عرض الأسعار':'View Pricing'"></a>
        </div>
        <p class="text-sm text-gray-400 mt-4" x-text="lang==='ar'?'بدون بطاقة دفع — كل المميزات لمدة 30 يوم':'No credit card — all features for 30 days'"></p>
    </div>
</section>

<!-- Stats -->
<section class="bg-navy-dark dark:bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach($stats as $stat)
        <div>
            <div class="text-4xl font-extrabold text-gold"><span data-counter="{{ (int) ($stat['value'] ?? 0) }}">0</span>{{ $stat['suffix'] ?? '' }}</div>
            <div class="text-sm text-gray-300 mt-2" x-text="lang==='ar'?'{{ addslashes($stat['label_ar'] ?? '') }}':'{{ addslashes($stat['label_en'] ?? '') }}'"></div>
        </div>
        @endforeach
    </div>
</section>

<!-- Features -->
<section id="features" class="py-20 bg-gray-50 dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy dark:text-white mb-3" x-text="lang==='ar'?'كل ما يحتاجه مكتبك في منصة واحدة':'Everything your office needs in one platform'"></h2>
            <p class="text-gray-500 dark:text-gray-400" x-text="lang==='ar'?'أدوات متكاملة لإدارة القضايا والعملاء والمالية باحترافية':'Integrated tools to manage cases, clients, and finances professionally'"></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($features as $feature)
            <div class="scroll-animate opacity-0 bg-white dark:bg-gray-900 rounded-2xl p-7 shadow-sm hover:shadow-lg transition-shadow border border-gray-100 dark:border-gray-800">
                <div class="w-12 h-12 rounded-xl bg-navy/5 dark:bg-gold/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-navy dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $featureIcons[$feature['icon'] ?? ''] ?? $featureIcons['document'] }}"/></svg>
                </div>
                <h3 class="text-lg font-bold text-navy dark:text-white mb-2" x-text="lang==='ar'?'{{ addslashes($feature['title_ar'] ?? '') }}':'{{ addslashes($feature['title_en'] ?? '') }}'"></h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed" x-text="lang==='ar'?'{{ addslashes($feature['desc_ar'] ?? '') }}':'{{ addslashes($feature['desc_en'] ?? '') }}'"></p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Us -->
<section id="why-us" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy dark:text-white mb-3" x-text="lang==='ar'?'لماذا تختار ميزان؟':'Why choose Mizan?'"></h2>
            <p class="text-gray-500 dark:text-gray-400" x-text="lang==='ar'?'أسباب تجعل ميزان الخيار الأمثل لمكتبك':'Reasons that make Mizan the ideal choice for your office'"></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($whyUs as $item)
            <div class="scroll-animate opacity-0 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gold/10 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $whyIcons[$item['icon'] ?? ''] ?? $whyIcons['star'] }}"/></svg>
                </div>
                <h3 class="text-lg font-bold text-navy dark:text-white mb-2" x-text="lang==='ar'?'{{ addslashes($item['title_ar'] ?? '') }}':'{{ addslashes($item['title_en'] ?? '') }}'"></h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed" x-text="lang==='ar'?'{{ addslashes($item['desc_ar'] ?? '') }}':'{{ addslashes($item['desc_en'] ?? '') }}'"></p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="py-20 bg-gray-50 dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy dark:text-white mb-3" x-text="lang==='ar'?'خطط تناسب كل مكتب':'Plans for every office'"></h2>
            <p class="text-gray-500 dark:text-gray-400" x-text="lang==='ar'?'ابدأ بتجربة مجانية شهر كامل، ثم اختر ما يناسبك':'Start with a free month, then choose what fits'"></p>
        </div>
        @include('saas.partials.plans-grid', ['plans' => $plans])
    </div>
</section>

<!-- Contact -->
<section id="contact" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy dark:text-white mb-3" x-text="lang==='ar'?'تواصل معنا':'Contact Us'"></h2>
            <p class="text-gray-500 dark:text-gray-400" x-text="lang==='ar'?'هل لديك سؤال؟ فريقنا جاهز لمساعدتك':'Have a question? Our team is ready to help'"></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Info -->
            <div class="space-y-5">
                @if(!empty($contact['phone']))
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-gold/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-navy dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11 11 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-navy dark:text-white text-sm" x-text="lang==='ar'?'الهاتف':'Phone'"></div>
                        <div class="text-gray-500 dark:text-gray-400 text-sm mt-1" dir="ltr">{{ $contact['phone'] }}</div>
                    </div>
                </div>
                @endif
                @if(!empty($contact['email']))
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-gold/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-navy dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-navy dark:text-white text-sm" x-text="lang==='ar'?'البريد الإلكتروني':'Email'"></div>
                        <a href="mailto:{{ $contact['email'] }}" class="text-gray-500 dark:text-gray-400 text-sm mt-1 hover:text-gold block" dir="ltr">{{ $contact['email'] }}</a>
                    </div>
                </div>
                @endif
                @if(!empty($contact['whatsapp']))
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 018.413 3.488 11.82 11.82 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-navy dark:text-white text-sm">واتساب</div>
                        <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" class="text-gray-500 dark:text-gray-400 text-sm mt-1 hover:text-green-500 block" x-text="lang==='ar'?'تواصل عبر واتساب':'Chat on WhatsApp'"></a>
                    </div>
                </div>
                @endif
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-navy/5 dark:bg-gold/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-navy dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-navy dark:text-white text-sm" x-text="lang==='ar'?'العنوان':'Address'"></div>
                        <div class="text-gray-500 dark:text-gray-400 text-sm mt-1" x-text="lang==='ar'?'{{ addslashes($contact['address_ar'] ?? '') }}':'{{ addslashes($contact['address_en'] ?? '') }}'"></div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div
                x-data="{
                    form: { name:'', email:'', phone:'', subject:'', message:'' },
                    loading:false, success:false, error:'',
                    async submit() {
                        this.loading = true; this.error = '';
                        try {
                            const res = await fetch('{{ route('platform.contact') }}', {
                                method:'POST',
                                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
                                body: JSON.stringify(this.form)
                            });
                            const data = await res.json();
                            if (data.success) { this.success = true; this.form = {name:'',email:'',phone:'',subject:'',message:''}; }
                            else { this.error = data.message || (lang==='ar'?'حدث خطأ':'An error occurred'); }
                        } catch(e) { this.error = lang==='ar'?'فشل الاتصال بالخادم':'Connection failed'; }
                        this.loading = false;
                    }
                }"
                class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700"
            >
                <div x-show="success" x-cloak class="text-center py-10">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy dark:text-white mb-2" x-text="lang==='ar'?'تم الإرسال بنجاح!':'Sent successfully!'"></h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6" x-text="lang==='ar'?'سنتواصل معك في أقرب وقت':'We will contact you soon'"></p>
                    <button @click="success=false" class="text-gold hover:underline text-sm" x-text="lang==='ar'?'إرسال رسالة أخرى':'Send another message'"></button>
                </div>

                <form x-show="!success" @submit.prevent="submit()" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input x-model="form.name" type="text" required :placeholder="lang==='ar'?'الاسم':'Name'" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 px-3 py-2.5 text-sm focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                        <input x-model="form.email" type="email" required dir="ltr" :placeholder="lang==='ar'?'البريد الإلكتروني':'Email'" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 px-3 py-2.5 text-sm focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input x-model="form.phone" type="text" dir="ltr" :placeholder="lang==='ar'?'الهاتف (اختياري)':'Phone (optional)'" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 px-3 py-2.5 text-sm focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                        <input x-model="form.subject" type="text" :placeholder="lang==='ar'?'الموضوع':'Subject'" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 px-3 py-2.5 text-sm focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
                    <textarea x-model="form.message" required rows="4" :placeholder="lang==='ar'?'رسالتك':'Your message'" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 px-3 py-2.5 text-sm focus:border-gold focus:ring-1 focus:ring-gold outline-none"></textarea>
                    <p x-show="error" x-cloak x-text="error" class="text-red-500 text-sm"></p>
                    <button type="submit" :disabled="loading" class="w-full py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-bold transition-colors disabled:opacity-60">
                        <span x-show="!loading" x-text="lang==='ar'?'إرسال الرسالة':'Send Message'"></span>
                        <span x-show="loading" x-cloak x-text="lang==='ar'?'جاري الإرسال...':'Sending...'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-navy dark:bg-gray-950 text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4" x-text="lang==='ar'?'جاهز لتطوير إدارة مكتبك؟':'Ready to upgrade your office?'"></h2>
        <p class="text-gray-200 mb-8" x-text="lang==='ar'?'ابدأ تجربتك المجانية الآن — شهر كامل بكل المميزات وبدون بطاقة دفع.':'Start your free trial now — a full month with all features and no credit card.'"></p>
        <a href="{{ route('register.plans') }}" class="inline-block px-10 py-4 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-lg transition-colors shadow-lg" x-text="lang==='ar'?'ابدأ مجاناً الآن':'Start Free Now'"></a>
    </div>
</section>
@endsection
