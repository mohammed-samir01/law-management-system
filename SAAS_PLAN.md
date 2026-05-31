# خطة تحويل ميزان إلى منصة SaaS متعددة المكاتب

## الهدف
تحويل النظام من نظام لمكتب واحد إلى منصة SaaS تستقبل اشتراكات من مكاتب محاماة متعددة، مع:
- صفحة تسويقية للمنصة نفسها
- صفحة تعريفية لكل مكتب على path خاص
- نظام خطط واشتراكات
- تدفق تسجيل ذاتي للمكاتب الجديدة

---

## البنية المستهدفة

```
mizan.com/                    ← صفحة تسويقية للمنصة (جديدة)
mizan.com/pricing             ← صفحة الأسعار (جديدة)
mizan.com/register            ← تسجيل مكتب جديد (جديدة)
mizan.com/offices/{slug}      ← صفحة تعريفية لكل مكتب (جديدة)
mizan.com/admin               ← لوحة تحكم مشتركة (موجودة - لا تتغير)
mizan.com/portal              ← بوابة عملاء مشتركة (موجودة - لا تتغير)
```

---

## المرحلة 1: Migrations + Models

### 1.1 إنشاء migration للخطط
**الملف:** `database/migrations/2026_06_01_000001_create_plans_table.php`

```php
Schema::create('plans', function (Blueprint $table) {
    $table->id();
    $table->json('name');                          // {'ar': 'احترافي', 'en': 'Professional'}
    $table->string('slug')->unique();              // basic, pro, enterprise
    $table->decimal('price_monthly', 10, 2)->default(0);
    $table->decimal('price_yearly', 10, 2)->default(0);
    $table->unsignedInteger('max_users')->default(5);
    $table->unsignedInteger('max_cases')->default(50);
    $table->unsignedInteger('max_storage_mb')->default(1024);
    $table->boolean('ai_enabled')->default(false);
    $table->boolean('custom_branding')->default(false);
    $table->boolean('is_active')->default(true);
    $table->unsignedTinyInteger('sort_order')->default(0);
    $table->timestamps();
});
```

### 1.2 إنشاء migration للاشتراكات
**الملف:** `database/migrations/2026_06_01_000002_create_subscriptions_table.php`

```php
Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('office_id')->constrained()->cascadeOnDelete();
    $table->foreignId('plan_id')->constrained()->restrictOnDelete();
    $table->enum('status', ['trial', 'active', 'past_due', 'cancelled', 'expired'])->default('trial');
    $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('current_period_start')->nullable();
    $table->timestamp('current_period_end')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->string('payment_method')->nullable();
    $table->string('payment_reference')->nullable();
    $table->timestamps();
});
```

### 1.3 Model: Plan
**الملف:** `app/Models/Plan.php`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasTranslations;
    protected $guarded = [];
    public $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'price_monthly'    => 'decimal:2',
            'price_yearly'     => 'decimal:2',
            'ai_enabled'       => 'boolean',
            'custom_branding'  => 'boolean',
            'is_active'        => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
```

### 1.4 Model: Subscription
**الملف:** `app/Models/Subscription.php`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'trial_ends_at'         => 'datetime',
            'current_period_start'  => 'datetime',
            'current_period_end'    => 'datetime',
            'cancelled_at'          => 'datetime',
        ];
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return match($this->status) {
            'active' => $this->current_period_end?->isFuture() ?? false,
            'trial'  => $this->trial_ends_at?->isFuture() ?? false,
            default  => false,
        };
    }
}
```

### 1.5 تعديل Office Model
**الملف:** `app/Models/Office.php`
**إضافة:**
```php
use Illuminate\Database\Eloquent\Relations\HasOne;

public function subscription(): HasOne
{
    return $this->hasOne(Subscription::class)->latestOfMany();
}

public function activePlan(): ?Plan
{
    $sub = $this->subscription;
    return ($sub && $sub->isActive()) ? $sub->plan : null;
}

public function hasActiveSubscription(): bool
{
    return $this->subscription?->isActive() ?? false;
}
```

---

## المرحلة 2: PlanSeeder

**الملف:** `database/seeders/PlanSeeder.php`

```php
<?php
namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'           => ['ar' => 'تجريبي', 'en' => 'Trial'],
                'slug'           => 'trial',
                'price_monthly'  => 0,
                'price_yearly'   => 0,
                'max_users'      => 3,
                'max_cases'      => 10,
                'max_storage_mb' => 512,
                'ai_enabled'     => false,
                'custom_branding'=> false,
                'sort_order'     => 0,
            ],
            [
                'name'           => ['ar' => 'أساسي', 'en' => 'Basic'],
                'slug'           => 'basic',
                'price_monthly'  => 199,
                'price_yearly'   => 1990,
                'max_users'      => 5,
                'max_cases'      => 50,
                'max_storage_mb' => 2048,
                'ai_enabled'     => false,
                'custom_branding'=> false,
                'sort_order'     => 1,
            ],
            [
                'name'           => ['ar' => 'احترافي', 'en' => 'Professional'],
                'slug'           => 'pro',
                'price_monthly'  => 499,
                'price_yearly'   => 4990,
                'max_users'      => 15,
                'max_cases'      => 200,
                'max_storage_mb' => 10240,
                'ai_enabled'     => true,
                'custom_branding'=> false,
                'sort_order'     => 2,
            ],
            [
                'name'           => ['ar' => 'مؤسسي', 'en' => 'Enterprise'],
                'slug'           => 'enterprise',
                'price_monthly'  => 999,
                'price_yearly'   => 9990,
                'max_users'      => 9999,
                'max_cases'      => 9999,
                'max_storage_mb' => 102400,
                'ai_enabled'     => true,
                'custom_branding'=> true,
                'sort_order'     => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
```

**تسجيل في DatabaseSeeder:**
```php
$this->call([
    RolesAndPermissionsSeeder::class,
    DemoDataSeeder::class,
    DocumentTemplateSeeder::class,
    PlanSeeder::class,
]);
```

---

## المرحلة 3: الصفحة التسويقية للمنصة

### 3.1 SaasLandingController
**الملف:** `app/Http/Controllers/SaasLandingController.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\Plan;

class SaasLandingController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('saas.landing', compact('plans'));
    }

    public function pricing()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('saas.pricing', compact('plans'));
    }
}
```

### 3.2 تعديل LandingController
**الملف:** `app/Http/Controllers/LandingController.php`
**إضافة method جديدة:**

```php
public function office(string $slug)
{
    $office = Office::withoutGlobalScopes()
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    $settings = array_replace_recursive($this->getDefaultSettings(), $office->settings ?? []);

    return view('landing.index', compact('settings'));
}
```

### 3.3 تحديث routes/web.php

```php
// القديم (احذف):
Route::get('/', [LandingController::class, 'index'])->name('home');

// الجديد (أضف):
Route::get('/', [SaasLandingController::class, 'index'])->name('home');
Route::get('/pricing', [SaasLandingController::class, 'pricing'])->name('pricing');
Route::get('/offices/{slug}', [LandingController::class, 'office'])->name('office.landing');

// تسجيل المكاتب (جديد):
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [OnboardingController::class, 'showPlans'])->name('plans');
    Route::post('/plan', [OnboardingController::class, 'selectPlan'])->name('plan.select');
    Route::get('/setup', [OnboardingController::class, 'showSetup'])->name('setup');
    Route::post('/setup', [OnboardingController::class, 'createOffice'])->name('create');
    Route::get('/success', [OnboardingController::class, 'success'])->name('success');
});
```

### 3.4 View: resources/views/saas/landing.blade.php

**محتوى الصفحة (بنفس ستايل landing الحالي — Tailwind + Alpine.js):**
- **Header:** شعار المنصة + nav (Features, Pricing, Login)
- **Hero:** "منصة إدارة مكاتب المحاماة الأكثر احترافية" + CTA "ابدأ تجربتك المجانية"
- **Stats:** عدد المكاتب، القضايا، العملاء
- **Features:** (6 بطاقات) إدارة القضايا، الفواتير، الجلسات، الوثائق، الذكاء الاصطناعي، بوابة العملاء
- **Pricing:** الخطط من `$plans`
- **CTA Final:** "ابدأ الآن مجاناً لمدة 14 يوماً"
- **Footer:** روابط + حقوق

### 3.5 View: resources/views/saas/pricing.blade.php

**4 خطط في Grid:**
```
تجريبي | أساسي | احترافي (موصى به) | مؤسسي
مجاناً | 199 ج.م/شهر | 499 ج.م/شهر | 999 ج.م/شهر
```
كل خطة تعرض: السعر، الحدود، المميزات، زر "اشترك الآن"

---

## المرحلة 4: تدفق التسجيل (Onboarding)

### 4.1 OnboardingController
**الملف:** `app/Http/Controllers/OnboardingController.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OnboardingController extends Controller
{
    public function showPlans()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('onboarding.plans', compact('plans'));
    }

    public function selectPlan(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        session(['onboarding_plan_id' => $request->plan_id]);
        return redirect()->route('register.setup');
    }

    public function showSetup()
    {
        if (! session('onboarding_plan_id')) {
            return redirect()->route('register.plans');
        }
        $plan = Plan::findOrFail(session('onboarding_plan_id'));
        return view('onboarding.setup', compact('plan'));
    }

    public function createOffice(Request $request)
    {
        $validated = $request->validate([
            'office_name_ar' => 'required|string|max:255',
            'office_name_en' => 'nullable|string|max:255',
            'slug'           => 'required|string|max:100|unique:offices,slug|regex:/^[a-z0-9\-]+$/',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:255|unique:offices,email',
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        $planId = session('onboarding_plan_id');
        $plan   = Plan::findOrFail($planId);

        DB::transaction(function () use ($validated, $plan) {
            $office = Office::create([
                'name'      => ['ar' => $validated['office_name_ar'], 'en' => $validated['office_name_en'] ?? ''],
                'slug'      => $validated['slug'],
                'phone'     => $validated['phone'],
                'email'     => $validated['email'],
                'is_active' => true,
            ]);

            $user = User::create([
                'name'      => $validated['admin_name'],
                'email'     => $validated['admin_email'],
                'password'  => Hash::make($validated['admin_password']),
                'office_id' => $office->id,
            ]);
            $user->assignRole('office_admin');

            Subscription::create([
                'office_id'          => $office->id,
                'plan_id'            => $plan->id,
                'status'             => $plan->slug === 'trial' || $plan->price_monthly == 0 ? 'trial' : 'active',
                'billing_cycle'      => 'monthly',
                'trial_ends_at'      => now()->addDays(14),
                'current_period_start' => now(),
                'current_period_end'   => now()->addMonth(),
            ]);
        });

        session()->forget('onboarding_plan_id');
        return redirect()->route('register.success');
    }

    public function success()
    {
        return view('onboarding.success');
    }
}
```

### 4.2 Views للـ Onboarding

**`resources/views/onboarding/plans.blade.php`**
- نفس ستايل الصفحات الحالية (Navy + Gold)
- عرض الخطط الـ 4 في grid
- كل خطة: الاسم، السعر، المميزات، زر "اختر هذه الخطة"
- Step indicator: 1 اختر خطتك → 2 بيانات المكتب → 3 تم

**`resources/views/onboarding/setup.blade.php`**
- Form فيه:
  - اسم المكتب (عربي / إنجليزي)
  - المعرف الفريد (slug) — يظهر preview: `mizan.com/offices/your-slug`
  - الهاتف والبريد الإلكتروني
  - اسم المسؤول / الإيميل / الباسورد
- Step indicator: 1 ✅ → **2 بيانات المكتب** → 3 تم

**`resources/views/onboarding/success.blade.php`**
- أيقونة ✅ كبيرة
- رسالة: "تم إنشاء مكتبك بنجاح!"
- بيانات الدخول
- زر: "ادخل على لوحة التحكم"

---

## المرحلة 5: Middleware الاشتراك

### 5.1 CheckSubscription Middleware
**الملف:** `app/Http/Middleware/CheckSubscription.php`

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! $user->office_id) {
            return $next($request);
        }

        // super_admin مش محتاج subscription
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        $office = $user->office;

        if (! $office || ! $office->hasActiveSubscription()) {
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
```

### 5.2 تسجيل Middleware
**الملف:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'client.portal'       => EnsureClientRole::class,
        'check.subscription'  => CheckSubscription::class,
    ]);
})
```

### 5.3 تطبيق Middleware على Admin
**الملف:** `app/Providers/Filament/AdminPanelProvider.php`
```php
->authMiddleware([
    Authenticate::class,
    \App\Http\Middleware\CheckSubscription::class,
])
```

### 5.4 Route لانتهاء الاشتراك
**الملف:** `routes/web.php`
```php
Route::middleware('auth')->get('/subscription/expired', function () {
    return view('subscription.expired');
})->name('subscription.expired');
```

**الملف:** `resources/views/subscription/expired.blade.php`
- رسالة: "انتهى اشتراكك"
- زر: "جدّد اشتراكك" → يروح لصفحة pricing
- زر: تواصل مع الدعم

---

## المرحلة 6: Filament Resources للـ Super Admin

### 6.1 PlanResource
**الملف:** `app/Filament/Resources/PlanResource.php`
- Form: name (ar/en), slug, price_monthly, price_yearly, max_users, max_cases, ai_enabled, is_active
- Table: name, slug, price_monthly, subscriptions count, is_active
- مرئي لـ super_admin فقط

### 6.2 SubscriptionResource
**الملف:** `app/Filament/Resources/SubscriptionResource.php`
- Table: office name, plan name, status, billing_cycle, current_period_end
- Filters: status, plan
- Actions: activate, cancel, extend
- مرئي لـ super_admin فقط

### 6.3 SubscriptionStatusWidget
**الملف:** `app/Filament/Widgets/SubscriptionStatusWidget.php`
- يظهر في الـ dashboard لكل مكتب
- يعرض: اسم الخطة، الحالة، تاريخ الانتهاء
- لو قرب الانتهاء (أقل من 7 أيام) → warning باللون الأصفر
- زر: "ترقية الخطة"

---

## ترتيب التنفيذ (خطوة خطوة)

```
1. php artisan make:migration create_plans_table
2. php artisan make:migration create_subscriptions_table
3. إنشاء Plan.php model
4. إنشاء Subscription.php model
5. تعديل Office.php model (إضافة العلاقات)
6. إنشاء PlanSeeder + تسجيله في DatabaseSeeder
7. php artisan migrate
8. php artisan db:seed --class=PlanSeeder
9. إنشاء SaasLandingController
10. تعديل LandingController (إضافة office method)
11. تعديل routes/web.php
12. إنشاء views/saas/landing.blade.php
13. إنشاء views/saas/pricing.blade.php
14. إنشاء OnboardingController
15. إنشاء views/onboarding/plans.blade.php
16. إنشاء views/onboarding/setup.blade.php
17. إنشاء views/onboarding/success.blade.php
18. إنشاء CheckSubscription middleware
19. تسجيل middleware في bootstrap/app.php
20. تطبيقه على AdminPanelProvider
21. إنشاء views/subscription/expired.blade.php
22. إنشاء PlanResource في Filament
23. إنشاء SubscriptionResource في Filament
24. إنشاء SubscriptionStatusWidget
```

---

## التحقق من النجاح

| الاختبار | المتوقع |
|----------|---------|
| `GET /` | يعرض صفحة المنصة التسويقية |
| `GET /pricing` | يعرض 4 خطط بالأسعار |
| `GET /offices/amer` | يعرض صفحة مكتب عامر |
| `GET /register` | يعرض اختيار الخطة |
| تسجيل مكتب جديد | ينشئ office + user + subscription |
| الدخول بـ office_admin | يدخل admin panel بشكل طبيعي |
| بعد انتهاء trial | يُعاد التوجيه لـ /subscription/expired |
| `super@mizan.test` | يرى PlanResource + SubscriptionResource |
| Widget في dashboard | يعرض الخطة الحالية والتاريخ |

---

## ملاحظات مهمة

1. **الـ DemoDataSeeder** — بعد التنفيذ يجب إضافة subscription للمكتب الموجود:
```php
Subscription::create([
    'office_id' => $office->id,
    'plan_id'   => Plan::where('slug', 'pro')->first()->id,
    'status'    => 'active',
    'billing_cycle' => 'monthly',
    'current_period_start' => now(),
    'current_period_end'   => now()->addYear(),
]);
```

2. **الـ super_admin** — لا يحتاج subscription ويتجاوز الـ middleware

3. **الصفحة التسويقية** — تستخدم نفس Alpine.js و Tajawal font الموجودين حالياً

4. **Payment للاشتراكات** — هذه المرحلة لا تشمل دفع حقيقي للاشتراكات (يدوي من super_admin)، الدفع الإلكتروني للاشتراكات يُضاف لاحقاً
