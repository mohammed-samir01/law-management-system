# خطة تحويل ميزان إلى منصة SaaS (نسخة كاملة ومفصّلة)

## Context — لماذا هذا التغيير؟
النظام الحالي يعمل كنظام multi-tenant بعزل `office_id`، لكنه مبني عملياً لمكتب واحد:
- الصفحة الرئيسية `/` تعرض أول مكتب نشط فقط
- لا يوجد نظام خطط/اشتراكات
- لا يوجد تسجيل ذاتي للمكاتب
- لا يوجد تحصيل مالي من المكاتب

الهدف: تحويله لمنصة اشتراكات حيث تسجّل المكاتب ذاتياً، تأخذ تجربة مجانية، ثم تدفع أونلاين لتفعيل اشتراكها — وأنت (مالك المنصة) تدير كل شيء من لوحة `/admin` بدور `super_admin`.

**قرارات المستخدم المعتمدة:**
1. **اسم المنصة الأم: «ميزان»** — هي الشركة/المنصة التي تشترك فيها المكاتب والمحامون. كل مكتب (مثل «مكتب عامر للمحاماة») مجرد عميل مشترك داخل ميزان.
2. **تجربة مجانية شهر كامل (30 يوم)** — تلقائية لأي مكتب/محامي جديد بمجرد التسجيل، بكل المميزات، بدون بطاقة دفع. بعد انتهاء الشهر يجب الدفع لمتابعة الاستخدام.
3. **تحصيل الاشتراك: أونلاين بالكارت** (بوابة على مستوى المنصة)
4. **الإدارة: نفس لوحة `/admin`** محمية بدور `super_admin`
5. **Routing: path-based** (`mizan.com/offices/{slug}`) — بدون subdomain

---

## هوية المنصة «ميزان»
- **الصفحة الرئيسية `/`** = هوية ميزان (المنصة الأم) — شعار ميزان، عبارة تسويقية، الخطط، CTA "ابدأ مجاناً". هذه ليست صفحة أي مكتب.
- **`/offices/{slug}`** = هوية المكتب نفسه (عامر، إلخ) — اسمه وشعاره وبياناته من `settings`.
- **اسم التطبيق:** ضبط `APP_NAME=ميزان` في `.env` + استخدام اسم/شعار «ميزان» في كل صفحات SaaS وOnboarding وBilling والإيميلات. شعار ميزان كأصل ثابت في `public/images/` (أو إعداد منفصل `platform` لاحقاً)، منفصل عن لوجو كل مكتب.
- **التمييز البصري:** صفحات ميزان تستخدم هوية المنصة؛ صفحات المكاتب تبقى بهوية كل مكتب (Navy/Gold الحالية أو ألوان المكتب من `settings.branding`).

### إدارة الصفحات التعريفية (مهم — تحكّم كامل)
| الصفحة | من يتحكم؟ | كيف؟ |
|--------|----------|------|
| صفحة ميزان `/` (الشركة الأم) | **super_admin فقط** | صفحة جديدة `PlatformSettingsPage` في `/admin` — تعدّل العنوان/الوصف/المميزات/الإحصائيات/الشعار/التواصل. تُحفظ في جدول `platform_settings` (صف واحد singleton) |
| صفحة المكتب `/offices/{slug}` | **office_admin** (كل مكتب صفحته) | **موجود بالفعل** عبر `LandingSettingsPage` التي تعدّل `offices.settings` JSON |

يعني: أنت تتحكم في صفحة ميزان، وكل مكتب يتحكم في صفحته — كلٌّ من لوحته بدون تعارض.

---

## الخطط والأسعار (بالجنيه المصري — اعتماد: اقتصادي)
| الخطة | الشهري | السنوي | المستخدمون | القضايا | AI | علامة مخصصة |
|------|--------|--------|-----------|---------|----|----|
| **تجربة مجانية** | 0 | 0 | 15 | 200 | ✅ | ❌ | (شهر كامل تلقائي، كل المميزات) |
| **أساسي** | 199 ج.م | 1990 ج.م | 5 | 50 | ❌ | ❌ |
| **احترافي** | 499 ج.م | 4990 ج.م | 15 | 200 | ✅ | ❌ |
| **مؤسسي** | 999 ج.م | 9990 ج.م | غير محدود | غير محدود | ✅ | ✅ |
العملة الافتراضية `EGP`. الأسعار **قابلة للتعديل** لاحقاً من `PlanResource` في لوحة التحكم بدون كود.

---

## 💰 توضيح التكاليف (نقطة مهمة)

| البند | التكلفة |
|------|---------|
| Laravel 12, Filament 3.3, كل حزم Spatie, DomPDF, إلخ | **مجاني (MIT)** |
| كل الكود الجديد في هذه الخطة | **مجاني** |
| **حزم جديدة مطلوبة** | **لا شيء — صفر حزم جديدة** |
| فتح حساب تاجر Paymob (مصر) أو Stripe | **مجاني** |
| **رسوم البوابة لكل عملية دفع اشتراك فعلية** | **~2.75% (Paymob) / ~2.9% (Stripe)** تُخصم من كل دفعة |

**الخلاصة:** صفر تكلفة برمجية وصفر حزم مدفوعة. لا نحتاج `laravel/spark` (مدفوع) ولا حتى `laravel/cashier` — لأننا نعيد استخدام كلاسات البوابات الموجودة (`StripeGateway`, `PaymobGateway`) التي تعمل بـ HTTP مباشر. المال الوحيد هو نسبة البوابة على كل دفعة تحصّلها فعلياً.

---

## البنية المستهدفة

```
mizan.com/                    ← صفحة تسويقية للمنصة (جديدة)
mizan.com/pricing             ← صفحة الأسعار (جديدة)
mizan.com/register            ← تسجيل مكتب جديد + اختيار خطة (جديدة)
mizan.com/offices/{slug}      ← صفحة تعريفية لكل مكتب (جديدة)
mizan.com/admin               ← لوحة تحكم المكاتب + إدارة المنصة (super_admin)
mizan.com/admin/billing       ← صفحة دفع/تجديد الاشتراك (جديدة)
mizan.com/portal              ← بوابة العملاء (موجودة - لا تتغير)
mizan.com/billing/webhook     ← استقبال تأكيد الدفع من البوابة (جديدة)
```

### اتجاه الأموال — فرق جوهري
- **الموجود حالياً:** مكتب → يحصّل من عملائه (مفاتيح بوابة لكل مكتب في جدول `payment_gateways`)
- **الجديد (اشتراكات):** المنصة → تحصّل من المكاتب (مفاتيح بوابة **واحدة** للمنصة في `.env`)

لذلك بوابة المنصة منفصلة تماماً عن بوابات المكاتب، ومفاتيحها في `config/services.php` وليست في قاعدة البيانات.

---

## الحزم المستخدمة (كلها موجودة بالفعل — لا تثبيت جديد)
- `filament/filament ^3.3` — لوحة التحكم وكل الـ Resources
- `spatie/laravel-permission ^6` — أدوار super_admin/office_admin
- `spatie/laravel-translatable ^6` — أسماء الخطط (ar/en)
- `Illuminate\Support\Facades\Http` — البوابات تتصل بـ Paymob/Stripe عبره
- Tailwind 4 + Alpine.js — صفحات التسويق والتسجيل (نفس ستايل `layouts/landing.blade.php`)
- كلاسات البوابات الموجودة: `app/Services/Payment/Gateways/PaymobGateway.php` و`StripeGateway.php` (تُعاد استخدامها كما هي)

---

## الخطوة 0: إنشاء البرانش وحفظ الخطة
```bash
git checkout -b saas-platform
```
ثم تحديث `SAAS_PLAN.md` في جذر المشروع بمحتوى هذه الخطة (نسخة كاملة محفوظة داخل المشروع).

---

## المرحلة 1: قاعدة البيانات والـ Models

### 1.1 Migrations (3 ملفات)

**`database/migrations/xxxx_create_plans_table.php`**
```php
Schema::create('plans', function (Blueprint $table) {
    $table->id();
    $table->json('name');                          // {'ar':'احترافي','en':'Professional'}
    $table->string('slug')->unique();              // trial, basic, pro, enterprise
    $table->decimal('price_monthly', 10, 2)->default(0);
    $table->decimal('price_yearly', 10, 2)->default(0);
    $table->string('currency', 3)->default('EGP');
    $table->unsignedInteger('max_users')->default(5);
    $table->unsignedInteger('max_cases')->default(50);
    $table->unsignedInteger('max_storage_mb')->default(1024);
    $table->boolean('ai_enabled')->default(false);
    $table->boolean('custom_branding')->default(false);
    $table->json('features')->nullable();          // قائمة مميزات نصية للعرض
    $table->boolean('is_active')->default(true);
    $table->unsignedTinyInteger('sort_order')->default(0);
    $table->timestamps();
});
```

**`database/migrations/xxxx_create_subscriptions_table.php`**
```php
Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('office_id')->constrained()->cascadeOnDelete();
    $table->foreignId('plan_id')->constrained()->restrictOnDelete();
    $table->enum('status', ['trial','active','past_due','cancelled','expired'])->default('trial');
    $table->enum('billing_cycle', ['monthly','yearly'])->default('monthly');
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('current_period_start')->nullable();
    $table->timestamp('current_period_end')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->timestamps();
});
```

**`database/migrations/xxxx_create_platform_settings_table.php`** (إعدادات صفحة ميزان — singleton)
```php
Schema::create('platform_settings', function (Blueprint $table) {
    $table->id();
    $table->json('data')->nullable();   // {hero, features, stats, contact, branding}
    $table->timestamps();
});
```

**`database/migrations/xxxx_create_subscription_payments_table.php`** (سجل مدفوعات الاشتراك — يطابق نمط Invoice/Payment الموجود)
```php
Schema::create('subscription_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
    $table->foreignId('office_id')->constrained()->cascadeOnDelete();
    $table->decimal('amount', 10, 2);
    $table->string('currency', 3)->default('EGP');
    $table->enum('billing_cycle', ['monthly','yearly']);
    $table->string('gateway');                     // paymob / stripe
    $table->string('gateway_transaction_id')->nullable();
    $table->string('reference')->nullable();       // order_id من البوابة
    $table->enum('status', ['pending','completed','failed','refunded'])->default('pending');
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
});
```

### 1.2 Models (3 جديدة + تعديل Office)

**`app/Models/Plan.php`** — `HasTranslations` على `name`، casts للأرقام والـ booleans، `features` => array، `hasMany(Subscription)`. بدون global scope (الخطط عامة على مستوى المنصة).

**`app/Models/Subscription.php`** — `belongsTo(Office)`, `belongsTo(Plan)`, `hasMany(SubscriptionPayment)`. دوال مساعدة:
```php
public function isActive(): bool   // active + current_period_end مستقبلي
public function onTrial(): bool     // trial + trial_ends_at مستقبلي
public function isUsable(): bool    // isActive() || onTrial()
public function daysLeft(): int     // أيام متبقية للتجربة أو الفترة
```
بدون global scope (super_admin يرى كل الاشتراكات).

**`app/Models/SubscriptionPayment.php`** — `belongsTo(Subscription)`, `belongsTo(Office)`, casts.

**`app/Models/PlatformSetting.php`** (singleton لإعدادات صفحة ميزان) — `data` => array. دالة `static current(): array` تجيب الصف الأول (أو تنشئه) وتدمجه مع `getDefaultPlatformSettings()` بنفس نمط `LandingController::getDefaultSettings()` الموجود.

**تعديل `app/Models/Office.php`** — إضافة:
```php
public function subscription(): HasOne   // hasOne(Subscription)->latestOfMany()
public function hasUsableSubscription(): bool  // subscription?->isUsable() ?? false
public function activePlan(): ?Plan
```

---

## المرحلة 2: بوابة المنصة (إعادة استخدام الكلاسات الموجودة)

### 2.1 إعدادات المنصة في `config/services.php`
```php
'platform_billing' => [
    'gateway'        => env('PLATFORM_BILLING_GATEWAY', 'paymob'), // paymob | stripe
    'paymob' => [
        'api_key'        => env('PAYMOB_API_KEY'),
        'integration_id' => env('PAYMOB_INTEGRATION_ID'),
        'iframe_id'      => env('PAYMOB_IFRAME_ID'),
        'hmac_secret'    => env('PAYMOB_HMAC_SECRET'),
    ],
    'stripe' => [
        'secret_key'     => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
],
```
**إضافات `.env` (وثّقها في `.env.example`):**
```
PLATFORM_BILLING_GATEWAY=paymob
PAYMOB_API_KEY=
PAYMOB_INTEGRATION_ID=
PAYMOB_IFRAME_ID=
PAYMOB_HMAC_SECRET=
```

### 2.2 `app/Services/Billing/PlatformBillingService.php` (جديد)
خدمة خفيفة تُنشئ كلاس البوابة الموجود بمفاتيح المنصة (وليس مفاتيح مكتب):
```php
public static function gateway(): PaymentGatewayInterface
{
    $name   = config('services.platform_billing.gateway');
    $config = config("services.platform_billing.$name");
    $class  = ['paymob' => PaymobGateway::class, 'stripe' => StripeGateway::class][$name];
    return new $class($config);   // نفس كلاسات app/Services/Payment/Gateways
}

public static function priceFor(Plan $plan, string $cycle): float  // monthly|yearly
```
**ملاحظة:** `PaymobGateway::charge()` يُرجع `data.payment_url` (redirect flow) — يطابق تماماً تدفق `Portal\InvoiceController::pay()` الموجود. نعيد استخدام نفس النمط: charge → `redirect()->away($payment_url)` → webhook/callback يؤكد.

---

## المرحلة 3: الصفحة التسويقية + صفحة المكتب

### 3.1 `app/Http/Controllers/SaasLandingController.php` (جديد)
- `index()` → يقرأ `$platform = PlatformSetting::current()` + `$plans = Plan::active()` → `view('saas.landing', compact('platform','plans'))` (المحتوى من إعدادات المنصة القابلة للتعديل)
- `pricing()` → `view('saas.pricing', compact('plans'))`

### 3.2 تعديل `app/Http/Controllers/LandingController.php`
إضافة `office(string $slug)` — تجيب المكتب بالـ slug وتعرض `landing.index` بنفس منطق `index()` الحالي (إعادة استخدام `getDefaultSettings()` و`array_replace_recursive`).

### 3.3 تعديل `routes/web.php`
```php
Route::get('/', [SaasLandingController::class, 'index'])->name('home');
Route::get('/pricing', [SaasLandingController::class, 'pricing'])->name('pricing');
Route::get('/offices/{slug}', [LandingController::class, 'office'])->name('office.landing');
// تسجيل المكاتب
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [OnboardingController::class, 'showPlans'])->name('plans');
    Route::post('/plan', [OnboardingController::class, 'selectPlan'])->name('plan.select');
    Route::get('/setup', [OnboardingController::class, 'showSetup'])->name('setup');
    Route::post('/setup', [OnboardingController::class, 'register'])->name('store');
    Route::get('/success', [OnboardingController::class, 'success'])->name('success');
});
```
**مهم:** نقل route الـ contact ليصبح per-office `POST /offices/{slug}/contact` حتى تذهب رسالة كل صفحة لمكتبها الصحيح (حالياً تذهب لأول مكتب فقط).

### 3.4 Views جديدة (Tailwind + Alpine، تعيد استخدام `layouts/landing.blade.php`)
- `resources/views/saas/landing.blade.php` — Hero للمنصة + 6 بطاقات مميزات + جدول الخطط + CTA "ابدأ مجاناً"
- `resources/views/saas/pricing.blade.php` — الخطط الـ4 في grid مع زر "اشترك"
- شريط تنقل المنصة + footer (يمكن عمل partial مشترك `saas/partials/navbar` + `footer`)

---

## المرحلة 4: تدفق التسجيل (Onboarding)

### 4.1 `app/Http/Controllers/OnboardingController.php` (جديد)
خطوات:
1. `showPlans()` — عرض الخطط
2. `selectPlan()` — يحفظ `plan_id` + `billing_cycle` في session
3. `showSetup()` — فورم بيانات المكتب + المسؤول (يتحقق وجود plan في session)
4. `register()` — validation ثم `DB::transaction`:
   - إنشاء `Office` (name ar/en, slug, phone, email, is_active=true)
   - إنشاء `User` (office_admin) عبر `assignRole('office_admin')`
   - إنشاء `Subscription` status=`trial`, `trial_ends_at = now()->addDays(30)` (شهر كامل بكل المميزات)
   - `Auth::login($user)`
   - **دائماً تبدأ بالتجربة المجانية أولاً** → `register.success`. حتى لو اختار خطة مدفوعة، يأخذ الشهر مجاناً ثم يُطالَب بالدفع عند الانتهاء (الخطة المختارة تُحفظ كتفضيل للترقية لاحقاً).
5. `success()` — صفحة ترحيب + زر دخول `/admin`

### 4.2 Views
- `resources/views/onboarding/plans.blade.php` (مؤشر خطوات 1/2/3)
- `resources/views/onboarding/setup.blade.php` (preview للـ slug: `mizan.com/offices/your-slug`)
- `resources/views/onboarding/success.blade.php`

---

## المرحلة 5: دفع/تجديد الاشتراك أونلاين

### 5.1 `app/Http/Controllers/SubscriptionBillingController.php` (جديد، auth)
- `show()` — صفحة الاشتراك الحالي (الخطة، الحالة، أيام متبقية، أزرار الترقية/الدفع)
- `checkout(Request)` — يختار خطة + دورة، ينشئ `SubscriptionPayment(status=pending)`، يستدعي `PlatformBillingService::gateway()->charge([...callback_url => billing.callback...])`، ثم `redirect()->away($payment_url)`
- `callback(Request)` — عند الرجوع من البوابة: يتحقق، يحدّث `SubscriptionPayment=completed` + `Subscription`(status=active, current_period_start=now, current_period_end=+شهر/سنة)
- `webhook(Request)` — endpoint عام (بدون auth، بدون CSRF) يستقبل تأكيد Paymob/Stripe، يتحقق HMAC عبر `gateway->handleWebhook()`، ويُفعّل الاشتراك (مصدر التأكيد الموثوق)

### 5.2 Routes
```php
Route::middleware('auth')->group(function () {
    Route::get('/admin/billing', [SubscriptionBillingController::class, 'show'])->name('billing.show');
    Route::post('/admin/billing/checkout', [SubscriptionBillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/admin/billing/callback', [SubscriptionBillingController::class, 'callback'])->name('billing.callback');
});
Route::post('/billing/webhook', [SubscriptionBillingController::class, 'webhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])->name('billing.webhook');
```

### 5.3 Views
- `resources/views/billing/show.blade.php` — صفحة الاشتراك + اختيار خطة/دورة + زر "ادفع"
- `resources/views/subscription/expired.blade.php` — "انتهى اشتراكك" + زر تجديد → `billing.show`

---

## المرحلة 6: Middleware حماية الاشتراك

### 6.1 `app/Http/Middleware/CheckSubscription.php` (جديد)
- يتجاوز: غير المسجّلين، `super_admin`، ومسارات `billing.*` و`subscription.expired`
- لو المكتب `! hasUsableSubscription()` → `redirect()->route('subscription.expired')`

### 6.2 تسجيل + تطبيق
- `bootstrap/app.php`: إضافة alias `'check.subscription'` بجانب `client.portal` الموجود
- `app/Providers/Filament/AdminPanelProvider.php`: إضافة `CheckSubscription::class` إلى `authMiddleware([...])` (بعد `Authenticate`)

---

## المرحلة 7: لوحة المنصة (Filament — super_admin فقط)

### 7.1 صلاحيات
تعديل `database/seeders/RolesAndPermissionsSeeder.php`: إضافة `'plans'` و`'subscriptions'` لمصفوفة `$resources` (تتولّد صلاحياتهما تلقائياً، وتُمنح كلها لـ super_admin).

### 7.2 `app/Filament/Resources/PlanResource.php`
- Form: name(ar/en), slug, price_monthly, price_yearly, currency, max_users/cases/storage, ai_enabled, custom_branding, features(Repeater), is_active, sort_order
- Table: name, slug, price_monthly, عدد الاشتراكات، is_active
- navigationGroup: `'الإدارة'`

### 7.3 `app/Filament/Resources/SubscriptionResource.php`
- Table: اسم المكتب، الخطة، الحالة(badge)، الدورة، تاريخ الانتهاء، أيام متبقية
- Filters: status, plan
- Actions يدوية (للحالات الخاصة): تفعيل، تمديد فترة، إلغاء
- RelationManager: `SubscriptionPayments` (سجل المدفوعات)

### 7.4 Policies (super_admin فقط)
`app/Policies/PlanPolicy.php` + `SubscriptionPolicy.php` — تعيد `super_admin` في كل الدوال (نفس نمط `OfficePolicy` المعدّل). الموديلات تُسجّل في `AppServiceProvider` لو كان هناك تسجيل صريح، وإلا Laravel يكتشفها تلقائياً.

### 7.5 `app/Filament/Widgets/SubscriptionStatusWidget.php`
- يظهر في `/admin` لكل office_admin: اسم الخطة، الحالة، أيام متبقية
- لو `daysLeft() <= 7` → تنبيه أصفر + زر "جدّد/رقّ" → `billing.show`
- مخفي لـ super_admin

### 7.6 `app/Filament/Pages/PlatformSettingsPage.php` (super_admin فقط) — التحكم في صفحة ميزان
- صفحة بنفس نمط `OfficeSettingsPage`/`LandingSettingsPage` الموجودة
- أقسام الفورم: العنوان الرئيسي + الوصف، الإحصائيات، 6 بطاقات مميزات (Repeater)، شعار ميزان (FileUpload)، بيانات تواصل المنصة
- `mount()` تملأ من `PlatformSetting::current()`؛ `save()` تحفظ في صف الـ singleton
- `navigationGroup: 'الإعدادات'`، مرئية لـ super_admin فقط (`canAccess()` يتحقق من الدور)

---

## المرحلة 8: Seeders والبيانات

### 8.1 `database/seeders/PlanSeeder.php` (جديد)
4 خطط بـ `updateOrCreate(['slug'=>...])`. **التجربة المجانية (30 يوم) تمنح كل مميزات خطة pro** أثناء الشهر، ثم يختار العميل خطة مدفوعة:
| slug | شهري | سنوي | users | cases | AI | branding | ملاحظة |
|------|------|------|-------|-------|----|----|----|
| trial | 0 | 0 | 15 | 200 | ✅ | ❌ | تلقائية 30 يوم لكل مكتب جديد، كل المميزات |
| basic | 199 | 1990 | 5 | 50 | ❌ | ❌ | |
| pro | 499 | 4990 | 15 | 200 | ✅ | ❌ | |
| enterprise | 999 | 9990 | 9999 | 9999 | ✅ | ✅ | |

### 8.2 تعديل `DatabaseSeeder.php`
إضافة `PlanSeeder::class` للقائمة.

### 8.3 تعديل `DemoDataSeeder.php`
بعد إنشاء المكتب، إنشاء `Subscription` نشط له (plan=pro, status=active, current_period_end=+سنة) حتى لا يُحظر المكتب التجريبي بالـ middleware.

---

## ترتيب التنفيذ
```
0.  git checkout -b saas-platform  + كتابة SAAS_PLAN.md في المشروع
1.  4 migrations (plans, subscriptions, platform_settings, subscription_payments)
2.  Models: Plan, Subscription, SubscriptionPayment, PlatformSetting + تعديل Office
3.  RolesAndPermissionsSeeder (+plans, +subscriptions) + PlanSeeder + DatabaseSeeder
4.  php artisan migrate + db:seed
5.  config/services.php (platform_billing) + .env (APP_NAME=ميزان) + .env.example
6.  PlatformBillingService
7.  SaasLandingController (يقرأ PlatformSetting) + LandingController::office + routes
8.  Views: saas/landing, saas/pricing (+ navbar/footer partials)
9.  OnboardingController + 3 views
10. SubscriptionBillingController + routes + 2 views (billing.show, subscription.expired)
11. CheckSubscription middleware + تسجيل + تطبيق على AdminPanelProvider
12. PlanResource + SubscriptionResource + Policies + Widget
13. PlatformSettingsPage (تحكم super_admin في صفحة ميزان)
14. تعديل DemoDataSeeder (subscription للمكتب الموجود)
15. npm run build + اختبار شامل
```

---

## التحقق (Verification)
| الاختبار | المتوقع |
|----------|---------|
| `GET /` | صفحة المنصة التسويقية (لا تعتمد على مكتب واحد) |
| `GET /pricing` | الخطط الـ4 بالأسعار |
| `GET /offices/amer` | صفحة مكتب عامر التعريفية |
| `GET /register` → اختيار خطة → بيانات → حفظ | إنشاء office + user(office_admin) + subscription(trial **30 يوم**) وتسجيل دخول تلقائي |
| office_admin يدخل `/admin` أثناء التجربة (الشهر المجاني) | يعمل طبيعي بكل المميزات + Widget يعرض أيام التجربة المتبقية |
| دفع خطة من `/admin/billing` | تحويل لبوابة المنصة → بعد الدفع: subscription=active وtransaction مسجّلة |
| webhook من البوابة | يفعّل الاشتراك حتى لو لم يرجع المستخدم |
| انتهاء الشهر المجاني بدون دفع | إعادة توجيه لـ `/subscription/expired` (ما عدا صفحات billing) |
| `super@mizan.test` | يرى Plans + Subscriptions + إعدادات منصة ميزان في `/admin`؛ office_admin لا يراها |
| super_admin يعدّل صفحة ميزان من `PlatformSettingsPage` | التغيير يظهر فوراً على `GET /` |
| office_admin يعدّل صفحته من `LandingSettingsPage` | التغيير يظهر فوراً على `GET /offices/{slug}` فقط |
| المكتب التجريبي (DemoData) | لا يُحظر (عنده اشتراك نشط) |

---

## ملاحظات ومخاطر
1. **مصدر تأكيد الدفع الموثوق هو الـ webhook** وليس رجوع المستخدم (قد يغلق المتصفح). الـ callback تحسين UX فقط.
2. **Paymob أنسب للسوق المصري** (تدفق redirect→iframe يطابق الكود الموجود). Stripe يتطلب Stripe.js في الواجهة لإتمام الكارت — مدعوم لكن أعقد؛ نبدأ بـ Paymob.
3. **لا نحتاج `laravel/cashier`** — نعيد استخدام كلاسات البوابات الموجودة. (Cashier بديل مجاني لكنه Stripe-only ويضيف تعقيداً غير مبرر هنا.)
4. **فرض حدود الخطة** (max_users/max_cases) خارج نطاق هذه المرحلة — يُضاف لاحقاً كـ checks في الـ Resources. الخطة الحالية تركّز على: تسجيل + تجربة + تحصيل + حظر عند الانتهاء.
5. **الاختبار بوضع البوابة التجريبي (`test_mode`)** قبل الإنتاج — البوابات تدعمه عبر config.
