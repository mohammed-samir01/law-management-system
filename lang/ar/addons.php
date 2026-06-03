<?php

return [
    // Messaging settings (super admin)
    'messaging'              => 'المراسلة (SMS / واتساب)',
    'messaging_desc'         => 'تُخزَّن المفاتيح مشفّرة. تُستخدم لإرسال رسائل SMS وواتساب للمكاتب التي فعّلت الإضافة.',
    'provider'               => 'مزوّد الخدمة',
    'sid'                    => 'Account SID',
    'token'                  => 'Auth Token',
    'sms_from'               => 'رقم المُرسِل (SMS)',
    'whatsapp_from'          => 'رقم المُرسِل (واتساب)',
    'test_connection'        => 'اختبار الاتصال',
    'connection_ok'          => 'نجح الاتصال بمزوّد المراسلة ✓',
    'connection_failed'      => 'فشل الاتصال — تحقق من المفاتيح',
    'not_configured'         => 'لم تُضبط بوابة المراسلة بعد',
    'saved'                  => 'تم حفظ إعدادات المراسلة',

    // Provider selection (per channel)
    'sms_provider'           => 'مزوّد الرسائل النصية (SMS)',
    'whatsapp_provider'      => 'مزوّد واتساب',
    'provider_twilio'        => 'Twilio',
    'provider_meta'          => 'Meta WhatsApp Cloud (رسمي)',
    'provider_egypt'         => 'بوابة SMS مصرية (HTTP)',
    'provider_vonage'        => 'Vonage',
    'meta_token'             => 'Access Token',
    'meta_phone_id'          => 'Phone Number ID',
    'eg_url'                 => 'رابط الـ API',
    'eg_method'              => 'طريقة الطلب',
    'eg_username'            => 'اسم المستخدم',
    'eg_password'            => 'كلمة المرور',
    'eg_sender'              => 'اسم المُرسِل (Sender ID)',
    'eg_lang'                => 'لغة الرسالة (1=إنجليزي، 2=عربي)',
    'vonage_key'             => 'API Key',
    'vonage_secret'          => 'API Secret',
    'vonage_from'            => 'اسم/رقم المُرسِل',
    'test_sms'               => 'اختبار SMS',
    'test_whatsapp'          => 'اختبار واتساب',
    'test_telegram'          => 'اختبار Telegram',

    // Telegram
    'telegram'               => 'Telegram (مجاني)',
    'telegram_enable'        => 'تفعيل قناة Telegram',
    'telegram_desc'          => 'قناة تذكير مجانية. يحتاج العميل ربط حسابه أولاً عبر زر «ربط تيليجرام» في صفحة العملاء.',
    'tg_bot_token'           => 'Bot Token',
    'tg_bot_username'        => 'اسم البوت (بدون @)',
    'tg_webhook_secret'      => 'سر الويبهوك (Webhook Secret)',
    'tg_register_webhook'    => 'تسجيل الويبهوك',
    'tg_webhook_ok'          => 'تم تسجيل ويبهوك Telegram ✓',
    'tg_webhook_failed'      => 'فشل تسجيل الويبهوك',
    'tg_link'                => 'ربط تيليجرام',
    'tg_no_bot'              => 'اضبط اسم البوت في إعدادات المراسلة أولاً',
    'tg_link_ready'          => 'انسخ هذا الرابط وأرسله للعميل ليفتحه ويضغط Start',

    // Gating / general
    'requires_addon'         => 'هذه الميزة تتطلب تفعيل الإضافة أولاً.',

    // SMS / WhatsApp message bodies
    'sms_hearing_reminder'   => 'تذكير: لديك جلسة في القضية :case بتاريخ :date — :location. (:app)',
    'wa_hearing_reminder'    => "🔔 تذكير بجلسة\nالقضية: :case\nالتاريخ: :date\nالمكان: :location\n\n:app",
    'wa_invoice'             => "🧾 فاتورة رقم :number بمبلغ :amount\nالحالة: :status\n\n:app",

    // E-signature
    'esign'                  => 'التوقيع الإلكتروني',
    'esign_send'             => 'إرسال للتوقيع',
    'esign_status'           => 'حالة التوقيع',
    'esign_status_none'      => 'بدون',
    'esign_status_pending'   => 'بانتظار التوقيع',
    'esign_status_signed'    => 'موقّعة',
    'esign_status_rejected'  => 'مرفوضة',
    'esign_sent'             => 'تم إرسال طلب التوقيع للعميل',
    'esign_no_client'        => 'لا يوجد عميل مرتبط بهذه الوثيقة',
    'esign_invalid_link'     => 'رابط التوقيع غير صالح أو منتهي الصلاحية',
    'esign_already_signed'   => 'تم توقيع هذه الوثيقة مسبقاً',
    'esign_page_title'       => 'توقيع المستند',
    'esign_instructions'     => 'يرجى مراجعة المستند ثم التوقيع في المربع أدناه.',
    'esign_sign_here'        => 'وقّع هنا',
    'esign_clear'            => 'مسح',
    'esign_submit'           => 'تأكيد التوقيع',
    'esign_success'          => 'تم توقيع المستند بنجاح. شكراً لك.',
    'esign_notify_subject'   => 'مستند بانتظار توقيعك',
    'esign_notify_body'      => 'لديك مستند بحاجة لتوقيعك الإلكتروني. اضغط الرابط للتوقيع من هاتفك.',
    'esign_notify_action'    => 'توقيع المستند',

    // Advanced AI
    'ai_advanced'            => 'AI متقدّم',
    'ai_draft_memo'          => 'صياغة مذكرة قانونية',
    'ai_compare_contracts'   => 'مقارنة عقدين',
    'ai_select_second_doc'   => 'اختر الوثيقة الثانية للمقارنة',
    'ai_predict'             => 'توقّع نتيجة القضية',
    'ai_predict_disclaimer'  => 'هذا تقدير استرشادي بالذكاء الاصطناعي ولا يُغني عن الرأي القانوني المتخصص.',
    'ai_prediction_result'   => 'توقّع نتيجة القضية',

    // Advanced reports
    'reports'                => 'التقارير والتحليلات',
    'reports_generate'       => 'توليد التقرير',
    'reports_export_pdf'     => 'تصدير PDF',
    'reports_type'           => 'نوع التقرير',
    'reports_date_from'      => 'من تاريخ',
    'reports_date_to'        => 'إلى تاريخ',
    'reports_financial'      => 'تقرير مالي (الإيرادات والمدفوعات)',
    'reports_cases'          => 'تقرير القضايا (حسب الحالة والنوع)',
    'reports_lawyers'        => 'أداء المحامين',

    // PWA
    'pwa_install'            => 'تثبيت التطبيق',

    // Phase 6 — e-invoice / court
    'einvoice_qr'           => 'رمز QR للفاتورة',
    'einvoice_qr_hint'      => 'هذا هو محتوى رمز الـ QR المتوافق (TLV/Base64). يُستخدم في طباعة الفاتورة الإلكترونية.',
    'court_sync'            => 'مزامنة من المحكمة',
    'court_not_configured'  => 'التكامل مع بوابة المحكمة غير مُفعّل بعد — يتطلب اتفاقية وصول رسمية. أدخل بيانات القضية يدوياً حالياً.',

    // Smart templates
    'tpl_smart_generate'     => 'إنشاء ذكي من قالب',
    'tpl_generated'          => 'تم إنشاء المستند من القالب ✓',

    // Time tracking & billing
    'time_entries'           => 'سجلّات الوقت',
    'time_add'               => 'تسجيل وقت',
    'time_minutes'           => 'الدقائق',
    'time_rate'              => 'سعر الساعة',
    'time_description'       => 'الوصف',
    'time_occurred_at'       => 'التاريخ',
    'time_billed'            => 'مفوتر',
    'time_amount'            => 'القيمة',
    'time_invoice_unbilled'  => 'فوترة الساعات غير المفوترة',
    'time_no_unbilled'       => 'لا توجد ساعات غير مفوترة',
    'time_invoice_created'   => 'تم إنشاء فاتورة بالساعات ✓',

    // Fee installments
    'inst_plan'              => 'خطة التقسيط',
    'inst_create'            => 'إنشاء خطة تقسيط',
    'inst_count'             => 'عدد الأقساط',
    'inst_first_due'         => 'تاريخ أول قسط',
    'inst_interval'          => 'الفترة بين الأقساط (أيام)',
    'inst_created'           => 'تم إنشاء خطة التقسيط ✓',
    'inst_amount'            => 'قيمة القسط',
    'inst_due_date'          => 'تاريخ الاستحقاق',
    'inst_status'            => 'الحالة',
    'inst_status_pending'    => 'مستحق',
    'inst_status_paid'       => 'مدفوع',
    'inst_status_overdue'    => 'متأخر',
    'inst_mark_paid'         => 'تحديد كمدفوع',
    'inst_installments'      => 'الأقساط',
    'inst_already'           => 'لهذه الفاتورة خطة تقسيط بالفعل',
];
