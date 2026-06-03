# 🚀 دليل نشر نظام ميزان على استضافة Hostinger المشتركة

> **نوع الاستضافة:** Shared Hosting (الخطة Business أو Premium — لأنها توفّر SSH + Cron Jobs)
> **المشروع:** Laravel 12 + Filament 3 — نظام إدارة مكاتب المحاماة (ميزان)
> **آخر تحديث:** يونيو 2026

---

## ⚠️ اقرأ هذا أولاً — قيود الاستضافة المشتركة

النظام **متوافق** مع الاستضافة المشتركة لأنه يستخدم `database` للـ (queue / cache / session) ولا يحتاج Redis. لكن انتبه لـ:

1. **لا يوجد Node.js** على Shared غالباً → نبني الأصول (Vite) **على جهازك** ونرفع مجلد `public/build` جاهزاً.
2. **لا يوجد Queue Worker دائم** → نشغّل المهام (AI / PDF / Email / OTP) عبر **Cron كل دقيقة**.
3. **document root** للدومين لازم يشاور على مجلد `/public` وليس جذر المشروع.
4. **متطلبات PHP**: لازم تتأكد من تفعيل الإكستنشنات: `intl`, `gd`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `curl`, `bcmath`, `zip`, `tokenizer`, `xml`. (معظمها مفعّل افتراضياً على Hostinger — تحقق من hPanel → PHP Configuration → PHP Extensions).
5. **إصدار PHP**: اضبطه على **8.2 أو 8.3** من hPanel.

---

## 📋 الترتيب العام للخطوات

```
المرحلة 1: التجهيز على جهازك المحلي
المرحلة 2: إعداد قاعدة البيانات على Hostinger
المرحلة 3: رفع الملفات
المرحلة 4: إعداد ملف .env
المرحلة 5: ضبط document root (مجلد public)
المرحلة 6: أوامر ما بعد النشر (migrate, cache, storage:link)
المرحلة 7: جدولة Cron (Scheduler + Queue)
المرحلة 8: الاختبار النهائي + الأمان
```

---

## ✅ المرحلة 1 — التجهيز على جهازك المحلي (قبل الرفع)

### 1.1 ابنِ أصول الواجهة (Vite)
على جهازك في مجلد المشروع:
```bash
npm install
npm run build
```
ده بينشئ مجلد `public/build` فيه الـ CSS و JS الجاهزين. **لازم ترفعه مع المشروع.**

### 1.2 ثبّت حزم الإنتاج فقط (بدون أدوات التطوير)
```bash
composer install --no-dev --optimize-autoloader
```
> `--no-dev` بتشيل debugbar و faker و pint... إلخ (حاجات مالهاش لزمة في الإنتاج وممكن تكون خطر أمني).

### 1.3 جهّز نسخة للرفع
المفروض يبقى عندك دلوقتي:
- ✅ مجلد `vendor/` كامل (حزم الإنتاج)
- ✅ مجلد `public/build/` (أصول Vite)
- ❌ **لا ترفع**: `node_modules/`، `.env` المحلي، `.git/`، `storage/*.key`

> إذا كان لديك SSH + Composer على السيرفر، يمكنك تخطّي رفع `vendor/` وتشغيل `composer install --no-dev` على السيرفر مباشرة (أنظف).

---

## ✅ المرحلة 2 — قاعدة البيانات على Hostinger

1. ادخل **hPanel → Databases → MySQL Databases**.
2. أنشئ قاعدة بيانات جديدة (مثلاً `uXXXX_mizan`).
3. أنشئ مستخدم وكلمة سر قوية، واربطه بالقاعدة بكل الصلاحيات.
4. **احفظ هذه القيم** — ستحتاجها في `.env`:
   - `DB_DATABASE` = اسم القاعدة الكامل
   - `DB_USERNAME` = اسم المستخدم الكامل
   - `DB_PASSWORD` = كلمة السر
   - `DB_HOST` = عادةً `localhost` على Hostinger (وليس `127.0.0.1` — تحقق من لوحة phpMyAdmin)

---

## ✅ المرحلة 3 — رفع الملفات

**الطريقة المفضّلة (SSH):** اضغط المشروع zip، ارفعه، وفكّه على السيرفر.
```bash
# على جهازك — تجاهل المجلدات الثقيلة/الحساسة
# ثم ارفع الـ zip عبر File Manager أو scp وفكّه على السيرفر
```

**الطريقة البديلة (File Manager):** ارفع ملف zip عبر hPanel → File Manager وفكّه.

### أين أرفع؟
الأنظف: ضع **جذر المشروع كامل** في مجلد **خارج** `public_html`، مثل:
```
/home/uXXXX/mizan/        ← جذر المشروع (app, vendor, routes, .env ...)
/home/uXXXX/public_html/  ← سيشير لمجلد public (المرحلة 5)
```

---

## ✅ المرحلة 4 — إعداد ملف `.env`

انسخ `.env.example` إلى `.env`، ثم اضبط القيم التالية:

```env
APP_NAME=ميزان
APP_ENV=production
APP_DEBUG=false                       # ⚠️ لازم false في الإنتاج
APP_URL=https://yourdomain.com        # دومينك الحقيقي بـ https

APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

LOG_LEVEL=error

# قاعدة البيانات (من المرحلة 2)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=uXXXX_mizan
DB_USERNAME=uXXXX_mizan
DB_PASSWORD=your-strong-password

# كلها database — متوافقة مع الاستضافة المشتركة (لا Redis)
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local

# البريد (SMTP) — يمكن ضبطه لاحقاً من لوحة التحكم بدلاً من هنا
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# OpenAI (أو اضبطه من لوحة التحكم — مشفّر في قاعدة البيانات)
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o
```

> 💡 **ملاحظة:** مفاتيح بوابات الدفع و OpenAI و SMTP يُفضّل ضبطها من **لوحة الإعدادات داخل النظام** (مشفّرة في قاعدة البيانات)، وليس في `.env`.

### توليد مفتاح التطبيق
بعد رفع الملفات (عبر SSH):
```bash
php artisan key:generate
```
> إذا لم يتوفّر SSH: ولّد المفتاح محلياً بـ `php artisan key:generate --show` وانسخ الناتج إلى `APP_KEY=` في `.env` يدوياً.

---

## ✅ المرحلة 5 — ضبط Document Root على مجلد `public`

السبب: مجلد المشروع الجذري يحتوي على `.env` وكودك — يجب ألا يكون متاحاً للويب. الدومين يجب أن يشاور على `/public` فقط.

### الطريقة (أ) — تغيير مجلد الجذر من hPanel (الأفضل لو متاح)
hPanel → الموقع → **Advanced → Change Website Root Directory** → اجعله يشير إلى مسار مجلد `public` الخاص بالمشروع.

### الطريقة (ب) — تعديل `index.php` (تعمل دائماً)
إذا لم يتح تغيير الجذر:
1. انسخ **محتويات** مجلد `public/` (بما فيها `build`, `index.php`, `.htaccess`) إلى `public_html/`.
2. عدّل `public_html/index.php` ليشير لمسار المشروع خارج `public_html`:
```php
// قبل
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// بعد (وجّهه لجذر المشروع الفعلي)
require __DIR__.'/../mizan/vendor/autoload.php';
$app = require_once __DIR__.'/../mizan/bootstrap/app.php';
```

### الطريقة (ج) — Symlink (لو عندك SSH وصلاحية حذف public_html)
```bash
rm -rf ~/public_html
ln -s ~/mizan/public ~/public_html
```

---

## ✅ المرحلة 6 — أوامر ما بعد النشر (عبر SSH)

من **جذر المشروع** (`~/mizan`):

```bash
# 1) صلاحيات الكتابة للمجلدات الحساسة
chmod -R 775 storage bootstrap/cache

# 2) ترحيل قاعدة البيانات + البيانات الأساسية (الأدوار والصلاحيات والخطط)
php artisan migrate --force
php artisan db:seed --force          # ⚠️ يُنشئ مستخدمين تجريبيين — راجع ملاحظة الأمان بالأسفل

# 3) ربط مجلد التخزين بالعامة (لرفع الملفات والصور)
php artisan storage:link

# 4) تهيئة الكاش للإنتاج (سرعة)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache-components
```

> ⚠️ **مهم:** أي تعديل لاحق على `.env` يتطلب إعادة `php artisan config:cache` (أو `config:clear`) وإلا لن يُقرأ التغيير.

> ⚠️ **بدون SSH:** نفّذ migrate و seed بإنشاء route مؤقت يستدعي `Artisan::call(...)` ثم احذفه، أو استورد قاعدة البيانات يدوياً عبر phpMyAdmin من نسخة محلية.

---

## ✅ المرحلة 7 — جدولة Cron (الأهم على الاستضافة المشتركة)

في **hPanel → Advanced → Cron Jobs**، أضف المهمتين التاليتين.
> استبدل `/usr/bin/php` بمسار PHP الصحيح على Hostinger (غالباً `/opt/alt/php82/usr/bin/php` أو ما يظهر في hPanel)، و `~/mizan` بمسار مشروعك.

### 7.1 جدولة Laravel (Scheduler) — كل دقيقة
يشغّل: تذكيرات الجلسات، فحص الفواتير المتأخرة، فرض الاشتراكات... إلخ.
```
* * * * * cd ~/mizan && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### 7.2 معالج المهام (Queue) — كل دقيقة
يشغّل: معالجة الذكاء الاصطناعي، توليد PDF، إرسال الإيميلات و OTP.
```
* * * * * cd ~/mizan && /usr/bin/php artisan queue:work --stop-when-empty --tries=3 --max-time=55 >> /dev/null 2>&1
```
> `--stop-when-empty` يجعله يعالج المهام المتراكمة ثم يخرج (مناسب للاستضافة المشتركة بدل الـ daemon الدائم). `--max-time=55` يضمن خروجه قبل تشغيل النسخة التالية.

> 🔁 **بديل أبسط:** لو الـ Scheduler شغّال، يمكنك الاعتماد على `schedule:run` لتشغيل الطابور تلقائياً — لكن إضافة مهمة queue منفصلة أكثر موثوقية للمهام الفورية (مثل إيميل OTP عند التسجيل).

---

## ✅ المرحلة 8 — الاختبار النهائي + الأمان

### اختبر:
- [ ] الصفحة الرئيسية (Landing) تفتح: `https://yourdomain.com`
- [ ] لوحة التحكم تفتح: `https://yourdomain.com/admin`
- [ ] تسجيل الدخول يعمل
- [ ] رفع صورة/مستند يعمل (يتأكد من `gd` + `storage:link`)
- [ ] تصدير PDF لفاتورة يعمل
- [ ] التسجيل يرسل إيميل OTP (يتأكد من الـ queue cron + SMTP)
- [ ] لا تظهر رسائل أخطاء تفصيلية (يعني `APP_DEBUG=false` شغّال)

### الأمان قبل الإطلاق الفعلي:
- [ ] `APP_DEBUG=false` و `APP_ENV=production` ✅
- [ ] **غيّر/احذف كلمات سر المستخدمين التجريبيين** من الـ seeder (`super@mizan.test` ... إلخ) — أو لا تشغّل `db:seed` على الإنتاج وأنشئ مستخدم super_admin يدوياً.
- [ ] فعّل **HTTPS** (شهادة SSL مجانية من hPanel → SSL).
- [ ] تأكد أن `.env` و `storage/` غير قابلين للوصول من الويب (تحقق أن document root على `public` فقط).
- [ ] راجع صلاحيات الملفات: لا تستخدم `777` أبداً — `775` للمجلدات و `644` للملفات.

---

## 🔄 ملحق — تحديث النظام لاحقاً (Deployment لاحق)

عند رفع تحديث جديد:
```bash
cd ~/mizan
php artisan down                     # وضع الصيانة
git pull                             # أو ارفع الملفات الجديدة
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
php artisan up                       # إنهاء الصيانة
```
> لا تنسَ بناء أصول Vite محلياً (`npm run build`) ورفع `public/build` إذا تغيّرت الواجهة.

---

## 🆘 حل المشكلات الشائعة

| المشكلة | السبب المحتمل | الحل |
|---------|----------------|------|
| صفحة بيضاء / خطأ 500 | `APP_KEY` فارغ أو صلاحيات `storage` | ولّد المفتاح + `chmod 775 storage` |
| `The intl extension is required` | إكستنشن غير مفعّل | hPanel → PHP Extensions → فعّل `intl` |
| الصور لا تُرفع | `gd` مُعطّل أو لا يوجد `storage:link` | فعّل `gd` + `php artisan storage:link` |
| الإيميلات/الذكاء الاصطناعي لا تعمل | الـ Queue cron غير مُعدّ | راجع المرحلة 7.2 |
| تعديل `.env` لا يظهر أثره | الإعدادات مخزّنة في الكاش | `php artisan config:clear` |
| CSS/JS لا تظهر | لم تُرفع `public/build` | ابنِ محلياً وارفع المجلد |
| `419 Page Expired` | مشكلة جلسة/HTTPS | تأكد `APP_URL` صحيح بـ https + `SESSION_DRIVER=database` |

---

## 📌 خلاصة سريعة (Checklist)

```
□ npm run build محلياً → ارفع public/build
□ composer install --no-dev --optimize-autoloader
□ أنشئ قاعدة بيانات على hPanel
□ ارفع المشروع خارج public_html
□ اضبط .env (DB + APP_URL + APP_DEBUG=false)
□ php artisan key:generate
□ وجّه document root إلى public
□ migrate --force + storage:link + config/route/view:cache
□ أضف Cron: schedule:run + queue:work --stop-when-empty
□ فعّل SSL (HTTPS)
□ غيّر كلمات السر التجريبية
□ اختبر: admin / login / رفع ملف / PDF / OTP
```
