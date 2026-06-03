@extends('layouts.saas')
@section('meta_robots')<meta name="robots" content="noindex,nofollow">@endsection

@section('title', 'أكمل ملف مكتبك — ميزان')

@section('content')
<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('onboarding.partials.steps', ['active' => 3])

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-navy mb-2">أكمل بيانات مكتبك</h1>
                <p class="text-gray-500 text-sm">هذه البيانات ستظهر فوراً على صفحتك العامة. يمكنك تعديلها لاحقاً من لوحة التحكم.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
                    <ul class="list-disc ps-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.profile.save') }}" class="space-y-6">
                @csrf

                {{-- Office Name --}}
                <div>
                    <h2 class="font-bold text-navy text-sm border-b border-gray-100 pb-2 mb-4">اسم المكتب</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم المكتب (عربي) <span class="text-red-500">*</span></label>
                            <input type="text" name="office_name_ar"
                                   value="{{ old('office_name_ar', $office->getTranslation('name','ar')) }}"
                                   required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم المكتب (إنجليزي)</label>
                            <input type="text" name="office_name_en"
                                   value="{{ old('office_name_en', $office->getTranslation('name','en')) }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm"
                                   dir="ltr">
                        </div>
                    </div>
                </div>

                {{-- Contact --}}
                <div>
                    <h2 class="font-bold text-navy text-sm border-b border-gray-100 pb-2 mb-4">بيانات التواصل</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الهاتف الأساسي <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone"
                                   value="{{ old('phone', $office->phone) }}"
                                   required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm"
                                   dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">هاتف ثانوي</label>
                            <input type="tel" name="phone2"
                                   value="{{ old('phone2', data_get($office->settings, 'contact.phone2')) }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm"
                                   dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
                            <input type="email" name="email"
                                   value="{{ old('email', $office->email) }}"
                                   required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm"
                                   dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                واتساب
                                <span class="text-gray-400 font-normal text-xs">(أرقام فقط بدون +)</span>
                            </label>
                            <input type="text" name="whatsapp"
                                   value="{{ old('whatsapp', data_get($office->settings, 'contact.whatsapp')) }}"
                                   placeholder="201xxxxxxxxx"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm"
                                   dir="ltr">
                        </div>
                    </div>
                </div>

                {{-- Address & Details --}}
                <div>
                    <h2 class="font-bold text-navy text-sm border-b border-gray-100 pb-2 mb-4">تفاصيل المكتب</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                            <input type="text" name="address_ar"
                                   value="{{ old('address_ar', data_get($office->settings, 'contact.address_ar')) }}"
                                   placeholder="مصر — القاهرة — مدينة نصر"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">سنة التأسيس</label>
                            <input type="number" name="founded_year"
                                   value="{{ old('founded_year', data_get($office->settings, 'hero.founded_year')) }}"
                                   placeholder="{{ date('Y') }}"
                                   min="1900" max="{{ date('Y') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm"
                                   dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ساعات العمل</label>
                            <input type="text" name="working_hours_ar"
                                   value="{{ old('working_hours_ar', data_get($office->settings, 'contact.working_hours_ar', 'الأحد — الخميس: ٩ ص — ٥ م')) }}"
                                   placeholder="الأحد — الخميس: ٩ ص — ٥ م"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 focus:border-gold focus:ring-1 focus:ring-gold outline-none text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-base transition-colors shadow-md">
                        حفظ وتابع ←
                    </button>
                    <a href="{{ route('register.success') }}"
                       class="text-center py-3 px-6 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 text-sm transition-colors">
                        تخطي الآن
                    </a>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection
