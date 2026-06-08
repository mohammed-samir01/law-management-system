# ميزان — نظام إدارة مكاتب المحاماة
# Mizan — Law Office Management System

نظام متكامل لإدارة مكاتب المحاماة مبني على Laravel 12 + Filament 3، يدعم اللغتين العربية والإنجليزية.

---

## Tech Stack

| Component | Version |
|---|---|
| Laravel | 12.x |
| PHP | 8.2+ |
| Filament Admin | 3.x |
| Database | MySQL 8.0 |
| Queue / Cache | Database (Redis اختياري) |
| Frontend | Tailwind CSS v4 + Alpine.js |

---

## Features

- **Multi-tenant** — كل مكتب محاماة بيانات مستقلة
- **Bilingual** — عربي (RTL) + إنجليزي كامل
- **Case Management** — إدارة القضايا والجلسات
- **Client Portal** — بوابة إلكترونية للعملاء
- **Document Management** — إدارة المستندات مع Spatie Media Library
- **Finance** — فواتير، مصروفات، بوابات دفع متعددة
- **AI Features** — تلخيص العقود والمستندات عبر OpenAI
- **PDF Generation** — تقارير وفواتير PDF
- **Push Notifications** — Firebase + إشعارات داخلية
- **2FA** — Google Authenticator

---

## Installation

### Requirements
- PHP 8.2+
- MySQL 8.0+
- Redis (اختياري — الافتراضي queue/cache على قاعدة البيانات)
- Node.js 20+
- Composer 2.x

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/mohammed-samir01/law-management-system-saas.git
cd law-management-system-saas

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install Node dependencies and build assets
npm install
npm run build

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Configure .env
# Set DB_*, MAIL_*, OPENAI_API_KEY, FIREBASE_* values

# 6. Run migrations and seeders
php artisan migrate --force
php artisan db:seed

# 7. Link storage
php artisan storage:link

# 8. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Demo Accounts

After running `php artisan db:seed`:

| Role | Email | Password |
|---|---|---|
| Super Admin | super@mizan.test | password |
| Office Admin | admin@mizan.test | password |
| Lawyer | lawyer1@mizan.test | password |
| Assistant | assistant@mizan.test | password |
| Client | client@mizan.test | password |

---

## Queue Worker (Production)

```bash
php artisan queue:work --tries=3 --timeout=90
```

Use **Supervisor** to keep the queue running:

```ini
[program:mizan-queue]
command=php /path/to/project/artisan queue:work database --tries=3
autostart=true
autorestart=true
user=www-data
```

---

## Scheduled Tasks

Add to crontab:

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Payment Gateways Supported

| Gateway | Region |
|---|---|
| Moyasar | Saudi Arabia |
| Mada (via Moyasar) | Saudi Arabia |
| InstaPay | Egypt |
| Vodafone Cash | Egypt |
| Stripe | Global |
| PayPal | Global |
| Bank Transfer | Manual |

---

## Roles & Permissions

| Role | Access |
|---|---|
| super_admin | Full system access |
| office_admin | Full access within office |
| lawyer | Assigned cases only |
| assistant | Limited read/write |
| client | Own cases via portal |

---

## Environment Variables Reference

See `.env.example` for all required variables.

Critical for production:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=` (generate with `php artisan key:generate`)
- `DB_*` — MySQL credentials
- `OPENAI_API_KEY` — For AI features
- `FIREBASE_*` — For push notifications

---

## License

Proprietary — All rights reserved.
