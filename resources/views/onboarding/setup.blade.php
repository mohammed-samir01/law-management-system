@extends('layouts.saas')
@section('meta_robots')<meta name="robots" content="noindex,nofollow">@endsection

@section('title', 'بيانات المكتب — ميزان')

@section('content')
<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('onboarding.partials.steps', ['active' => 2])

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8"
             x-data="{
                slug: '{{ old('slug', '') }}',
                password: '',
                showPassword: false,
                showConfirm: false,
                get hasMin()     { return this.password.length >= 8 },
                get hasUpper()   { return /[A-Z]/.test(this.password) },
                get hasNumber()  { return /[0-9]/.test(this.password) },
                get hasSpecial() { return /[^A-Za-z0-9]/.test(this.password) },
                get strength() {
                    const s = [this.hasMin, this.hasUpper, this.hasNumber, this.hasSpecial].filter(Boolean).length;
                    if (s <= 1) return 'weak';
                    if (s === 2) return 'fair';
                    if (s === 3) return 'good';
                    return 'strong';
                },
                get strengthLabel() {
                    return { weak: 'ضعيفة', fair: 'مقبولة', good: 'جيدة', strong: 'قوية' }[this.strength];
                },
                get strengthColor() {
                    return { weak: 'bg-red-500', fair: 'bg-orange-400', good: 'bg-yellow-400', strong: 'bg-green-500' }[this.strength];
                },
                get strengthWidth() {
                    return { weak: 'w-1/4', fair: 'w-2/4', good: 'w-3/4', strong: 'w-full' }[this.strength];
                },
                confirm: '',
                get passwordsMatch() { return this.confirm.length > 0 && this.password === this.confirm },
                get passwordsMismatch() { return this.confirm.length > 0 && this.password !== this.confirm }
             }">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-navy mb-2">أنشئ حساب مكتبك</h1>
                <p class="text-gray-500 text-sm">الخطة المختارة: <span class="font-bold text-gold">{{ $plan->getTranslation('name', 'ar') }}</span> — تبدأ بتجربة مجانية 30 يوم</p>
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

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf

                <div class="border-b border-gray-100 pb-2 mb-2">
                    <h2 class="font-bold text-navy text-sm">بيانات المكتب</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم المكتب (عربي) *</label>
                        <input type="text" name="office_name_ar" value="{{ old('office_name_ar') }}" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم المكتب (إنجليزي)</label>
                        <input type="text" name="office_name_en" value="{{ old('office_name_en') }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المعرّف الفريد (Slug) *</label>
                    <input type="text" name="slug" x-model="slug" value="{{ old('slug') }}" required
                           pattern="[a-z0-9\-]+" placeholder="my-office"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none ltr:text-left" dir="ltr">
                    <p class="text-xs text-gray-400 mt-1">رابط صفحتك: <span dir="ltr">{{ url('/offices') }}/<span class="text-gold font-mono" x-text="slug || 'your-slug'"></span></span></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Phone with country code --}}
                    <div x-data="{
                        dialCode: '{{ old('dial_code', '+20') }}',
                        phoneNum: '{{ old('phone_number') }}',
                        get fullPhone() { return this.dialCode + this.phoneNum; }
                    }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">هاتف المكتب *</label>
                        <div class="flex gap-2" dir="ltr">
                            <select x-model="dialCode"
                                    class="rounded-lg border border-gray-300 px-2 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none bg-white text-sm w-28 flex-shrink-0">
                                <option value="+20">🇪🇬 +20</option>
                                <option value="+966">🇸🇦 +966</option>
                                <option value="+971">🇦🇪 +971</option>
                                <option value="+965">🇰🇼 +965</option>
                                <option value="+974">🇶🇦 +974</option>
                                <option value="+973">🇧🇭 +973</option>
                                <option value="+968">🇴🇲 +968</option>
                                <option value="+962">🇯🇴 +962</option>
                                <option value="+961">🇱🇧 +961</option>
                                <option value="+964">🇮🇶 +964</option>
                                <option value="+218">🇱🇾 +218</option>
                                <option value="+212">🇲🇦 +212</option>
                                <option value="+216">🇹🇳 +216</option>
                                <option value="+213">🇩🇿 +213</option>
                                <option value="+1">🇺🇸 +1</option>
                                <option value="+44">🇬🇧 +44</option>
                            </select>
                            <input type="tel" x-model="phoneNum"
                                   placeholder="1001234567"
                                   required dir="ltr"
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                        </div>
                        {{-- Hidden input sends the full number --}}
                        <input type="hidden" name="phone" :value="fullPhone">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            إيميل التواصل للمكتب *
                            <span class="text-xs text-gray-400 font-normal">(يظهر للعملاء)</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
                </div>

                <div class="border-b border-gray-100 pb-2 mb-2 pt-4">
                    <h2 class="font-bold text-navy text-sm">بيانات المسؤول (حساب الدخول)</h2>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسمك الكامل *</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        إيميلك (لتسجيل الدخول) *
                        <span class="text-xs text-gray-400 font-normal">(سري — لا يظهر للعملاء)</span>
                    </label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required dir="ltr"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               name="admin_password" required dir="ltr"
                               x-model="password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Strength bar --}}
                    <div x-show="password.length > 0" class="mt-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-400">قوة كلمة المرور</span>
                            <span class="text-xs font-medium"
                                  :class="{
                                      'text-red-500':    strength === 'weak',
                                      'text-orange-400': strength === 'fair',
                                      'text-yellow-500': strength === 'good',
                                      'text-green-500':  strength === 'strong'
                                  }"
                                  x-text="strengthLabel"></span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300"
                                 :class="[strengthColor, strengthWidth]"></div>
                        </div>
                    </div>

                    {{-- Requirements --}}
                    <ul class="mt-3 space-y-1">
                        <li class="flex items-center gap-2 text-xs transition-colors" :class="hasMin ? 'text-green-600' : 'text-gray-400'">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="hasMin"  d="M5 13l4 4L19 7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="!hasMin" d="M12 12m-4 0a4 4 0 108 0"/>
                            </svg>
                            8 أحرف على الأقل
                        </li>
                        <li class="flex items-center gap-2 text-xs transition-colors" :class="hasUpper ? 'text-green-600' : 'text-gray-400'">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="hasUpper"  d="M5 13l4 4L19 7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="!hasUpper" d="M12 12m-4 0a4 4 0 108 0"/>
                            </svg>
                            حرف كبير واحد على الأقل (A-Z)
                        </li>
                        <li class="flex items-center gap-2 text-xs transition-colors" :class="hasNumber ? 'text-green-600' : 'text-gray-400'">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="hasNumber"  d="M5 13l4 4L19 7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="!hasNumber" d="M12 12m-4 0a4 4 0 108 0"/>
                            </svg>
                            رقم واحد على الأقل (0-9)
                        </li>
                        <li class="flex items-center gap-2 text-xs transition-colors" :class="hasSpecial ? 'text-green-600' : 'text-gray-400'">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="hasSpecial"  d="M5 13l4 4L19 7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x-show="!hasSpecial" d="M12 12m-4 0a4 4 0 108 0"/>
                            </svg>
                            رمز خاص واحد على الأقل (!@#$...)
                        </li>
                    </ul>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'"
                               name="admin_password_confirmation" required dir="ltr"
                               x-model="confirm"
                               :class="passwordsMatch ? 'border-green-400 focus:border-green-400 focus:ring-green-400' : (passwordsMismatch ? 'border-red-400 focus:border-red-400 focus:ring-red-400' : 'border-gray-300 focus:border-gold focus:ring-gold')"
                               class="w-full rounded-lg border px-3 py-2 pr-10 outline-none focus:ring-1">
                        <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p x-show="passwordsMatch"   class="mt-1.5 text-xs text-green-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        كلمتا المرور متطابقتان
                    </p>
                    <p x-show="passwordsMismatch" class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        كلمتا المرور غير متطابقتين
                    </p>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-bold text-lg transition-colors mt-4">
                    إنشاء الحساب وبدء التجربة المجانية
                </button>

                <p class="text-center text-xs text-gray-400">بإنشائك الحساب فأنت توافق على شروط الاستخدام. لن تُطالَب بأي دفع خلال الشهر المجاني.</p>
            </form>
        </div>
    </div>
</section>
@endsection
