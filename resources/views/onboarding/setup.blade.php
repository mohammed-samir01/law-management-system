@extends('layouts.saas')

@section('title', 'بيانات المكتب — ميزان')

@section('content')
<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('onboarding.partials.steps', ['active' => 2])

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8"
             x-data="{ slug: '' }">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الهاتف *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required dir="ltr"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">بريد المكتب *</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">بريدك الإلكتروني *</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required dir="ltr"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور *</label>
                        <input type="password" name="admin_password" required dir="ltr"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور *</label>
                        <input type="password" name="admin_password_confirmation" required dir="ltr"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-gold focus:ring-1 focus:ring-gold outline-none">
                    </div>
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
