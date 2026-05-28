# CLAUDE.md — Amer Law Office Management System

## 🎯 Project Identity

| Key | Value |
|-----|-------|
| Project Name | **Amer** (عامر) — Law Office Management System |
| Laravel Version | **12.x** (PHP 8.2+) |
| Admin Panel | **Filament 3.x** |
| Database | **MySQL 8.0** |
| Cache & Queue | **Redis** |
| Languages | **Arabic (RTL, default)** + English (LTR) |
| Audience | Law firms — Egypt & Saudi Arabia |

---

## 🏗️ Tech Stack (exact versions)

```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "filament/filament": "^3.0",
  "livewire/livewire": "^3.0",
  "spatie/laravel-permission": "^6.0",
  "spatie/laravel-translatable": "^6.0",
  "spatie/laravel-medialibrary": "^11.0",
  "spatie/laravel-activitylog": "^4.0",
  "laravel/sanctum": "^4.0",
  "barryvdh/laravel-dompdf": "^3.0",
  "openai-php/client": "^0.10.0"
}
```

---

## 📁 Project Structure

```
app/
├── Filament/
│   ├── Resources/          # One Resource per module
│   ├── Pages/              # Custom Filament pages
│   └── Widgets/            # Dashboard widgets
├── Models/                 # All Eloquent models
├── Services/               # Business logic
│   ├── Payment/            # Payment gateway abstraction
│   ├── AIService.php       # OpenAI integration
│   ├── PDFService.php      # PDF generation
│   └── NotificationService.php
├── Policies/               # One Policy per model
├── Http/
│   ├── Controllers/Api/    # RESTful API (v1)
│   └── Controllers/Portal/ # Client portal
└── Jobs/                   # Queued jobs

database/
├── migrations/             # Ordered migrations
├── seeders/                # All seeders
└── factories/              # Model factories

lang/
├── ar/                     # Arabic translations (default)
└── en/                     # English translations

resources/views/
├── filament/               # Filament customizations
└── portal/                 # Client portal Blade views

routes/
├── api.php                 # API routes (v1 prefix)
└── web.php                 # Portal routes
```

---

## 📐 Coding Rules (MUST follow always)

### General
- **Never** hardcode strings — always use `__('key')` translation helpers
- **Never** write raw SQL — always use Eloquent ORM
- **Always** use `office_id` scoping on every query (multi-tenant)
- **Always** add `office_id` to every model that belongs to an office
- **Always** use Form Request classes for validation — never validate in controllers
- **Always** use Resource classes for API responses
- **Always** wrap DB operations in transactions when touching multiple tables

### Naming Conventions
```
Models:          PascalCase singular        → Case, Client, Hearing
Controllers:     PascalCase + Controller    → CaseController
Filament:        PascalCase + Resource      → CaseResource
Services:        PascalCase + Service       → PaymentService
Jobs:            PascalCase + Job           → SendHearingReminderJob
Policies:        PascalCase + Policy        → CasePolicy
Migrations:      snake_case descriptive     → create_cases_table
Tables:          snake_case plural          → cases, hearing_notes
Columns:         snake_case                 → created_by, office_id
Lang keys:       snake_case with dots       → cases.create_new
```

### Models
```php
// Every model MUST have:
protected $guarded = [];                    // or explicit $fillable
protected static function booted(): void {
    // Always scope to office
    static::addGlobalScope('office', function ($q) {
        if (auth()->check() && auth()->user()->office_id) {
            $q->where('office_id', auth()->user()->office_id);
        }
    });
}
```

### Translatable Fields
```php
// Use spatie/laravel-translatable for all content fields
use Spatie\Translatable\HasTranslations;

public $translatable = ['name', 'description', 'notes'];
```

### Filament Resources
```php
// Every Resource MUST have:
// 1. Global search configured
// 2. Proper navigation group (in Arabic)
// 3. office_id injected on create
// 4. Eager loading to prevent N+1
// 5. Arabic labels on all fields
```

### Policies
```php
// Every model MUST have a Policy
// Register all policies in AuthServiceProvider
// Use Spatie permissions: 'view_cases', 'create_cases', etc.
```

---

## 🗄️ Database Schema Rules

### Every table MUST have:
```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
office_id       BIGINT UNSIGNED NOT NULL (FK → offices.id)  -- except offices table itself
created_by      BIGINT UNSIGNED NULLABLE (FK → users.id)
created_at      TIMESTAMP
updated_at      TIMESTAMP
deleted_at      TIMESTAMP NULLABLE  -- soft deletes on all tables
```

### Core Tables (build in this order):
```
1. offices
2. users
3. roles / permissions (Spatie)
4. clients
5. cases
6. hearings
7. documents
8. expenses
9. payments
10. invoices
11. payment_gateways
12. enforcement_files
13. powers_of_attorney
14. legislation
15. case_laws
16. notifications
17. support_tickets
18. ai_results
19. activity_log (Spatie)
```

---

## 🔐 Roles & Permissions

### Roles (seed these exactly):
```
super_admin     → full access to everything
office_admin    → full access within their office
lawyer          → access to assigned cases only
assistant       → limited read/write on cases
client          → portal access to own cases only
```

### Permission naming pattern:
```
view_{resource}         → view_cases, view_clients
create_{resource}       → create_cases
edit_{resource}         → edit_cases
delete_{resource}       → delete_cases
export_{resource}       → export_cases
manage_{resource}       → manage_payment_gateways (admin only)
```

---

## 💳 Payment Gateways

### Interface (MUST implement for each gateway):
```php
interface PaymentGatewayInterface {
    public function charge(array $data): array;
    public function refund(string $transactionId, float $amount): array;
    public function getStatus(string $transactionId): string;
    public function handleWebhook(Request $request): void;
    public function testConnection(): bool;
}
```

### Gateways to implement:
```
Egypt:         Paymob, InstaPay, Vodafone Cash
Saudi Arabia:  Moyasar, Mada (via Moyasar), PayTabs
Global:        Stripe, PayPal
Manual:        Bank Transfer (admin confirms manually)
```

### Gateway config storage:
```php
// Store in payment_gateways table, config column encrypted
// Never store API keys in .env for multi-tenant (per-office keys)
'config' => encrypt(json_encode([
    'api_key' => '...',
    'secret'  => '...',
]))
```

---

## 🌐 Arabic (RTL) Rules

```php
// config/app.php
'locale' => 'ar',
'fallback_locale' => 'en',

// Every Blade layout must have:
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

// Filament panel — add to PanelProvider:
->rtl()   // when locale is AR
->font('Tajawal')  // Arabic-friendly font

// All navigation labels in Arabic:
->navigationLabel('القضايا')
->navigationGroup('إدارة القضايا')
->modelLabel('قضية')
->pluralModelLabel('القضايا')
```

### Translation file structure:
```php
// lang/ar/cases.php
return [
    'cases'         => 'القضايا',
    'create_new'    => 'إضافة قضية جديدة',
    'case_number'   => 'رقم القضية',
    'case_type'     => 'نوع القضية',
    'status'        => 'الحالة',
    ...
];
```

---

## ⚡ Performance Rules

```php
// Always eager load in Filament Resources:
public static function getEloquentQuery(): Builder {
    return parent::getEloquentQuery()->with([
        'client', 'lawyers', 'hearings'
    ]);
}

// Cache expensive queries:
Cache::remember("office_{$officeId}_cases_stats", 300, fn() => ...);

// Dispatch heavy operations to queue:
// - PDF generation      → GeneratePDFJob
// - Email sending       → SendEmailJob
// - AI summarization    → AIProcessJob
// - Webhook handling    → ProcessWebhookJob
```

---

## 🚨 Error Handling Rules

```php
// Always use try/catch in Services:
public function charge(array $data): array {
    try {
        // payment logic
    } catch (PaymentException $e) {
        Log::error('Payment failed', ['error' => $e->getMessage(), 'data' => $data]);
        throw $e;
    }
}

// API responses always use this format:
return response()->json([
    'success' => true/false,
    'message' => __('messages.success'),
    'data'    => $resource,
    'errors'  => []
], $statusCode);
```

---

## 📋 Build Order (STRICT — follow exactly)

```
Phase 1 — Foundation
  ✅ Step 1:  Laravel 12 install + all composer packages
  ✅ Step 2:  Filament 3 install + AdminPanelProvider config
  ✅ Step 3:  All migrations (in schema order above)
  ✅ Step 4:  All Eloquent models with relationships + office scope
  ✅ Step 5:  Spatie roles + permissions seeder
  ✅ Step 6:  Auth (Sanctum + 2FA with pragmarx/google2fa)

Phase 2 — Core Modules
  ✅ Step 7:  Office & User Filament Resources
  ✅ Step 8:  Client Filament Resource + client portal auth
  ✅ Step 9:  Case Filament Resource (full CRUD + status flow)
  ✅ Step 10: Hearing Filament Resource + calendar widget

Phase 3 — Documents & Finance
  ✅ Step 11: Document management + Spatie Media Library
  ✅ Step 12: Expense + Payment + Invoice Filament Resources
  ✅ Step 13: Payment gateway abstraction + all 9 gateways
  ✅ Step 14: PDF generation (invoices, case reports)

Phase 4 — Advanced Modules
  ✅ Step 15: Enforcement files + POA modules
  ✅ Step 16: Legal reference system (legislation + case law)
  ✅ Step 17: Notification system (in-app + email + queue)
  ✅ Step 18: AI features (document summary + contract analysis)

Phase 5 — Polish
  ✅ Step 19: Dashboard widgets + charts (Chart.js)
  ✅ Step 20: Support ticket system
  ✅ Step 21: Settings module (office + system + gateways)
  ✅ Step 22: All Arabic/English lang files
  ✅ Step 23: Factories + seeders (demo data)
  ✅ Step 24: Docker setup + .env.example + README
```

---

## 🌱 Required Seed Data

```php
// Run: php artisan db:seed

// Users:
super@mizan.test     / password  → super_admin
admin@mizan.test     / password  → office_admin
lawyer1@mizan.test   / password  → lawyer
assistant@mizan.test / password  → assistant
client@mizan.test    / password  → client (portal access)

// Demo Office: "مكتب ميزان للمحاماة"
// 10 sample cases (mixed types + statuses)
// 5 hearings (upcoming + past)
// Sample documents, expenses, payments
```

---

## ✅ Definition of Done (per module)

Before moving to next module, verify:
- [ ] Migration runs without errors: `php artisan migrate`
- [ ] Model relationships work in Tinker
- [ ] Filament Resource loads without errors
- [ ] Policy is registered and tested
- [ ] Arabic + English labels are set
- [ ] No N+1 queries (check with Debugbar)
- [ ] office_id scoping works correctly

---

## 🚫 Common Mistakes to Avoid

```
❌ Don't use str_random() → use Str::random()
❌ Don't use DB::table() for business logic → use Models
❌ Don't put logic in controllers → use Services
❌ Don't forget office_id on every query
❌ Don't use $request->all() → use $request->validated()
❌ Don't hardcode Arabic strings in Blade → use __()
❌ Don't run AI/PDF/Email operations synchronously → always queue
❌ Don't store payment API keys in .env → store encrypted in DB
```

---

*CLAUDE.md — Amer v1.0 | Laravel 12 + Filament 3 | AR+EN*
