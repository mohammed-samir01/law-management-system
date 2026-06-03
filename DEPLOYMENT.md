# دليل النشر — منصة ميزان

## 1) خطوات النشر (بالترتيب)
```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=AddonSeeder --force   # لتحديث متجر الإضافات
php artisan storage:link
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 2) خدمات لازم تكون شغّالة على السيرفر
- **Queue worker** (إلزامي — الرسائل والإشعارات مصفّفة):
  `php artisan queue:work --tries=3` عبر Supervisor.
- **Cron / Scheduler** (للتذكيرات والآجال والاشتراكات):
  `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`
- **HTTPS (SSL)** — إلزامي للـ PWA (service worker) وروابط التوقيع/التقويم.
- **امتداد PHP GD** — لأيقونات الـ PWA المولّدة.
- **`APP_URL` صحيح** في `.env` — تُبنى منه روابط التوقيع والتقويم والإشعارات.

## 3) مفاتيح API — من أين تُضبط

### أ) من لوحة التحكم (super_admin → إعدادات المنصة) — مشفّرة في قاعدة البيانات
لا تحتاج لمسها في `.env`:
- **OpenAI** (تبويب الذكاء الاصطناعي) — مُستخدم في كل ميزات الـ AI بما فيها تطبيق الموبايل.
- **المراسلة** (تبويب المراسلة): SMS / واتساب / تيليجرام — Twilio / Meta Cloud / Vonage.
- **البريد / SMTP** (تبويب البريد).
- **بوابة الدفع** (تبويب الفوترة) — على مستوى المنصة + لكل مكتب.

> كل ما سبق له fallback تلقائي إلى `.env` لو تُرك فارغاً في اللوحة.

### ب) إعداد سيرفر / ملفات (بنية تحتية — تُضبط مرة واحدة، ليست في اللوحة عمداً)
- **إشعارات الموبايل (Firebase Cloud Messaging):**
  ضع ملف الحساب الخدمي في:
  `storage/app/firebase-credentials.json`
  (يُحمّل من Firebase Console → Project Settings → Service accounts → Generate new private key). بدونه تعمل بقية الإشعارات (داخل التطبيق/البريد) ويُتخطّى الـ Push بأمان.

- **النسخ الاحتياطي السحابي (S3) — اختياري:**
  النسخ الاحتياطي الحالي **تنزيل JSON فقط** ولا يحتاج مفاتيح. الرفع التلقائي إلى S3 (إن فُعّل مستقبلاً) يقرأ من `.env`:
  `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`.

## 4) تكاملات «معطّلة افتراضياً» (تحتاج اعتماد خارجي عند التفعيل)
هذه نقاط ربط جاهزة في الكود، لا تعمل حتى تُضبط — ولا تُعطّل أي وظيفة أساسية بدونها:
- **Google Calendar (مزامنة ثنائية):** حالياً يعمل تصدير ICS فقط (لا يحتاج إعداد). المزامنة الثنائية تحتاج OAuth.
- **ZATCA (التقديم الإلكتروني الفعلي):** توليد QR والتقرير الضريبي يعملان محلياً. التقديم لهيئة الضرائب يحتاج شهادة اعتماد (CSID).
- **بوابات المحاكم (Najiz/التقاضي المصري):** تعمل كـ stub يرد «غير مُفعّل»؛ التكامل الحقيقي يحتاج اتفاقية وصول رسمية.

## 5) إعدادات أولية بعد النشر
1. اضبط مفاتيح OpenAI + المراسلة + البريد + الدفع من اللوحة.
2. (اختياري) ضع `firebase-credentials.json` لتفعيل الـ Push.
3. فعّل التحقق بالإيميل (OTP) بعد ضبط SMTP ونجاح «إرسال إيميل اختبار».
