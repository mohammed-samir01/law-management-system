<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use App\Services\Billing\PlatformBillingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SuperAdminSettingsHub extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات المنصة';
    protected static ?string $title           = 'إعدادات المنصة';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view           = 'filament.pages.super-admin-settings-hub';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public function mount(): void
    {
        $row = PlatformSetting::singleton();

        $this->form->fill([
            // Homepage visibility
            'show_live_bar'       => PlatformSetting::get('homepage.show_live_bar', true),
            'show_before_after'   => PlatformSetting::get('homepage.show_before_after', true),
            'show_activity_feed'  => PlatformSetting::get('homepage.show_activity_feed', true),
            'show_trust_badges'   => PlatformSetting::get('homepage.show_trust_badges', true),

            // System
            'app_name'              => config('app.name'),
            'default_locale'        => config('app.locale', 'ar'),
            'timezone'              => config('app.timezone', 'Asia/Riyadh'),
            'announcement_enabled'  => PlatformSetting::get('announcement.enabled', false),
            'announcement_color'    => PlatformSetting::get('announcement.color', 'gold'),
            'announcement_text_ar'  => PlatformSetting::get('announcement.text_ar', ''),
            'announcement_text_en'  => PlatformSetting::get('announcement.text_en', ''),
            'developer_name'        => PlatformSetting::get('developer.name', 'Mohamed Shahin'),
            'developer_linkedin'    => PlatformSetting::get('developer.linkedin', ''),
            'platform_map_src'      => PlatformSetting::get('contact.map_embed_src', ''),

            // Billing
            'billing_gateway'    => $row->billing_gateway ?? 'paymob',
            'billing_test_mode'  => $row->billing_test_mode ?? true,
            'billing_config'     => $row->billing_config ?? [],

            // Mail
            'mail'                       => $row->mail_config ?? [],
            'otp_length'                 => PlatformSetting::get('otp.length', 6),
            'otp_ttl_minutes'            => PlatformSetting::get('otp.ttl_minutes', 15),
            'otp_max_attempts'           => PlatformSetting::get('otp.max_attempts', 5),
            'email_verification_enabled' => PlatformSetting::get('security.email_verification_enabled', false),

            // Security
            'rate_login'    => PlatformSetting::get('security.rate.login', 5),
            'rate_register' => PlatformSetting::get('security.rate.register', 3),
            'rate_contact'  => PlatformSetting::get('security.rate.contact', 5),
            'rate_otp'      => PlatformSetting::get('security.rate.otp', 3),
            'rate_uploads'  => PlatformSetting::get('security.rate.uploads', 30),
            'rate_ai'       => PlatformSetting::get('security.rate.ai', 20),
            'rate_api'      => PlatformSetting::get('security.rate.api', 120),
            'max_upload_kb' => PlatformSetting::get('media.max_upload_kb', 10240),
            'avatar_max_kb' => PlatformSetting::get('media.avatar_max_kb', 2048),

            // AI
            'openai_api_key' => PlatformSetting::singleton()->openai_api_key,
            'ai_model'       => PlatformSetting::get('ai.model', config('services.openai.model', 'gpt-4o')),
            'max_tokens'     => PlatformSetting::get('ai.max_tokens', 2000),
            'temperature'    => PlatformSetting::get('ai.temperature', 0.3),

            // Messaging (per-channel providers)
            'messaging'      => array_replace_recursive([
                'sms_provider'      => 'twilio',
                'whatsapp_provider' => 'twilio',
                'telegram_enabled'  => false,
            ], $row->messaging_config ?? []),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('hub')
                    ->tabs([

                        Forms\Components\Tabs\Tab::make('الصفحة الرئيسية')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Section::make('إظهار / إخفاء أقسام الصفحة الرئيسية')
                                    ->description('تحكم في ظهور كل قسم للزوار.')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_live_bar')
                                            ->label('شريط الإحصائيات الحية (عداد المكاتب)')
                                            ->helperText('يظهر بين الهيرو والأرقام')
                                            ->default(true),
                                        Forms\Components\Toggle::make('show_before_after')
                                            ->label('قسم المقارنة (قبل/بعد ميزان)')
                                            ->default(true),
                                        Forms\Components\Toggle::make('show_activity_feed')
                                            ->label('إشعارات النشاط الحي (Popup)')
                                            ->helperText('يظهر في الركن الأيسر للزوار')
                                            ->default(true),
                                        Forms\Components\Toggle::make('show_trust_badges')
                                            ->label('قسم الضمان والثقة (Trust Badges)')
                                            ->default(true),
                                    ])->columns(2),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('saveHomepage')
                                        ->label('حفظ إعدادات الصفحة الرئيسية')
                                        ->icon('heroicon-o-check')
                                        ->action('saveHomepage'),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('إعدادات النظام')
                            ->icon('heroicon-o-cog-8-tooth')
                            ->schema([
                                Forms\Components\Section::make('شريط الإشعار (Announcement Bar)')
                                    ->description('يظهر في أعلى الموقع لجميع الزوار. اتركه فارغاً لإخفائه.')
                                    ->schema([
                                        Forms\Components\Toggle::make('announcement_enabled')
                                            ->label('تفعيل الشريط')
                                            ->default(false),
                                        Forms\Components\Select::make('announcement_color')
                                            ->label('اللون')
                                            ->options([
                                                'gold'   => '🟡 ذهبي (عروض)',
                                                'red'    => '🔴 أحمر (عاجل)',
                                                'green'  => '🟢 أخضر (إيجابي)',
                                                'navy'   => '🔵 أزرق داكن',
                                            ])
                                            ->default('gold'),
                                        Forms\Components\TextInput::make('announcement_text_ar')
                                            ->label('نص الإشعار (عربي)')
                                            ->placeholder('🔥 عرض محدود — اشترك الآن واحصل على شهرين مجاناً')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('announcement_text_en')
                                            ->label('نص الإشعار (إنجليزي)')
                                            ->placeholder('🔥 Limited offer — Subscribe now and get 2 months free')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Forms\Components\Section::make('إعدادات النظام')
                                    ->schema([
                                        Forms\Components\TextInput::make('app_name')
                                            ->label('اسم التطبيق')
                                            ->required(),
                                        Forms\Components\Select::make('default_locale')
                                            ->label('اللغة الافتراضية')
                                            ->options(['ar' => 'العربية', 'en' => 'English'])
                                            ->required(),
                                        Forms\Components\Select::make('timezone')
                                            ->label('المنطقة الزمنية')
                                            ->options([
                                                'Asia/Riyadh'  => 'الرياض (AST)',
                                                'Africa/Cairo' => 'القاهرة (EET)',
                                                'UTC'          => 'UTC',
                                            ])
                                            ->required(),
                                    ])->columns(3),

                                Forms\Components\Section::make('خريطة Google Maps')
                                    ->description('افتح Google Maps → Share → Embed a map → انسخ src من الـ iframe فقط.')
                                    ->schema([
                                        Forms\Components\TextInput::make('platform_map_src')
                                            ->label('رابط الخريطة (src)')
                                            ->placeholder('https://www.google.com/maps/embed?pb=...')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('المطوّر (Powered By)')
                                    ->description('يظهر في فوتر صفحات جميع المكاتب.')
                                    ->schema([
                                        Forms\Components\TextInput::make('developer_name')
                                            ->label('اسم المطوّر')
                                            ->default('Mohamed Shahin'),
                                        Forms\Components\TextInput::make('developer_linkedin')
                                            ->label('رابط المطوّر')
                                            ->url()
                                            ->placeholder('https://...')
                                            ->extraInputAttributes(['dir' => 'ltr']),
                                    ])->columns(2),

                                Forms\Components\Section::make('إدارة الكاش')
                                    ->schema([
                                        Forms\Components\Placeholder::make('cache_info')
                                            ->label('معلومات الكاش')
                                            ->content('مسح الكاش يحذف ذاكرة التخزين المؤقت للتطبيق والمسارات والإعدادات.'),
                                    ]),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('saveSystem')
                                        ->label('حفظ إعدادات النظام')
                                        ->icon('heroicon-o-check')
                                        ->action('saveSystem'),
                                    Forms\Components\Actions\Action::make('clearCache')
                                        ->label('مسح الكاش')
                                        ->icon('heroicon-o-trash')
                                        ->color('warning')
                                        ->requiresConfirmation()
                                        ->action('clearCache'),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('بوابة الدفع')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Section::make('البوابة')
                                    ->description('اختر بوابة الدفع التي تُحصّل بها اشتراكات المكاتب. تُخزَّن المفاتيح مشفّرة بالكامل.')
                                    ->schema([
                                        Forms\Components\Select::make('billing_gateway')
                                            ->label('البوابة')
                                            ->options([
                                                'paymob' => 'Paymob (مصر — موصى به)',
                                                'stripe' => 'Stripe (عالمي)',
                                            ])
                                            ->default('paymob')
                                            ->required()
                                            ->live(),
                                        Forms\Components\Toggle::make('billing_test_mode')
                                            ->label('وضع الاختبار (Test Mode)')
                                            ->helperText('فعّله أثناء التجربة قبل الإطلاق.')
                                            ->default(true),
                                    ])->columns(2),

                                Forms\Components\Section::make('مفاتيح Paymob')
                                    ->visible(fn (Forms\Get $get) => $get('billing_gateway') === 'paymob')
                                    ->schema([
                                        Forms\Components\TextInput::make('billing_config.api_key')->label('API Key')->password()->revealable()->autocomplete(false),
                                        Forms\Components\TextInput::make('billing_config.integration_id')->label('Integration ID'),
                                        Forms\Components\TextInput::make('billing_config.iframe_id')->label('Iframe ID'),
                                        Forms\Components\TextInput::make('billing_config.hmac_secret')->label('HMAC Secret')->password()->revealable()->autocomplete(false),
                                    ])->columns(2),

                                Forms\Components\Section::make('مفاتيح Stripe')
                                    ->visible(fn (Forms\Get $get) => $get('billing_gateway') === 'stripe')
                                    ->schema([
                                        Forms\Components\TextInput::make('billing_config.secret_key')->label('Secret Key')->password()->revealable()->autocomplete(false),
                                        Forms\Components\TextInput::make('billing_config.webhook_secret')->label('Webhook Secret')->password()->revealable()->autocomplete(false),
                                    ])->columns(2),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('saveBilling')
                                        ->label('حفظ إعدادات بوابة الدفع')
                                        ->icon('heroicon-o-check')
                                        ->action('saveBilling'),
                                    Forms\Components\Actions\Action::make('testConnection')
                                        ->label('اختبار الاتصال')
                                        ->color('gray')
                                        ->icon('heroicon-o-signal')
                                        ->action('testConnection'),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('إعدادات البريد')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Forms\Components\Section::make('خادم البريد (SMTP)')
                                    ->description('تُخزَّن كلمة المرور مشفّرة. تُطبَّق هذه الإعدادات على إرسال كل رسائل النظام.')
                                    ->schema([
                                        Forms\Components\TextInput::make('mail.host')->label('Host')->placeholder('smtp.gmail.com'),
                                        Forms\Components\TextInput::make('mail.port')->label('Port')->numeric()->default(587),
                                        Forms\Components\TextInput::make('mail.username')->label('Username')->autocomplete(false),
                                        Forms\Components\TextInput::make('mail.password')->label('Password')->password()->revealable()->autocomplete(false),
                                        Forms\Components\Select::make('mail.encryption')->label('التشفير')->options(['tls' => 'TLS', 'ssl' => 'SSL', '' => 'بدون'])->default('tls'),
                                        Forms\Components\TextInput::make('mail.from_address')->label('بريد المُرسِل')->email()->placeholder('no-reply@mizan.com'),
                                        Forms\Components\TextInput::make('mail.from_name')->label('اسم المُرسِل')->default('ميزان'),
                                    ])->columns(2),

                                Forms\Components\Section::make('التحقق بالإيميل (OTP)')
                                    ->schema([
                                        Forms\Components\Toggle::make('email_verification_enabled')
                                            ->label('تفعيل التحقق بالإيميل للمستخدمين')
                                            ->helperText('فعّله بعد ضبط SMTP ونجاح "إرسال إيميل اختبار". قبل ذلك يبقى مغلقاً حتى لا يُقفل أحد.'),
                                        Forms\Components\TextInput::make('otp_length')->label('عدد أرقام الكود')->numeric()->minValue(4)->maxValue(8)->default(6),
                                        Forms\Components\TextInput::make('otp_ttl_minutes')->label('مدة صلاحية الكود (دقائق)')->numeric()->minValue(1)->default(15),
                                        Forms\Components\TextInput::make('otp_max_attempts')->label('أقصى عدد محاولات')->numeric()->minValue(1)->default(5),
                                    ])->columns(2),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('saveMail')
                                        ->label('حفظ إعدادات البريد')
                                        ->icon('heroicon-o-check')
                                        ->action('saveMail'),
                                    Forms\Components\Actions\Action::make('sendTest')
                                        ->label('إرسال إيميل اختبار')
                                        ->color('gray')
                                        ->icon('heroicon-o-paper-airplane')
                                        ->form([
                                            Forms\Components\TextInput::make('recipient')
                                                ->label('البريد الإلكتروني للاختبار')
                                                ->email()
                                                ->required()
                                                ->placeholder('you@example.com')
                                                ->helperText('استخدم بريداً حقيقياً — عناوين .test لا يمكن تسليمها.'),
                                        ])
                                        ->action(fn (array $data) => $this->sendTest($data['recipient'])),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('الأمان والوسائط')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Section::make('حدود المعدّل (طلبات لكل دقيقة لكل IP/مستخدم)')
                                    ->description('تحمي السيرفر من الإساءة. القيم تُطبَّق فوراً بعد الحفظ.')
                                    ->schema([
                                        Forms\Components\TextInput::make('rate_login')->label('تسجيل الدخول')->numeric()->minValue(1)->required(),
                                        Forms\Components\TextInput::make('rate_register')->label('التسجيل الجديد')->numeric()->minValue(1)->required(),
                                        Forms\Components\TextInput::make('rate_contact')->label('نماذج التواصل')->numeric()->minValue(1)->required(),
                                        Forms\Components\TextInput::make('rate_otp')->label('طلب رمز التحقق')->numeric()->minValue(1)->required(),
                                        Forms\Components\TextInput::make('rate_uploads')->label('رفع الملفات')->numeric()->minValue(1)->required(),
                                        Forms\Components\TextInput::make('rate_ai')->label('طلبات الذكاء الاصطناعي')->numeric()->minValue(1)->required(),
                                        Forms\Components\TextInput::make('rate_api')->label('واجهة API')->numeric()->minValue(1)->required(),
                                    ])->columns(3),

                                Forms\Components\Section::make('حدود رفع الملفات')
                                    ->schema([
                                        Forms\Components\TextInput::make('max_upload_kb')
                                            ->label('أقصى حجم ملف (كيلوبايت)')
                                            ->numeric()->minValue(256)->required()
                                            ->helperText('10240 = 10 ميجابايت'),
                                        Forms\Components\TextInput::make('avatar_max_kb')
                                            ->label('أقصى حجم صورة شخصية (كيلوبايت)')
                                            ->numeric()->minValue(128)->required(),
                                    ])->columns(2),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('saveSecurity')
                                        ->label('حفظ إعدادات الأمان')
                                        ->icon('heroicon-o-check')
                                        ->action('saveSecurity'),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('الذكاء الاصطناعي')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                        Forms\Components\Section::make('مفتاح OpenAI API')
                                    ->description('يُخزَّن مشفّراً. يُستخدم لجميع طلبات الذكاء الاصطناعي على المنصة. لو فارغ بيرجع لـ OPENAI_API_KEY في .env')
                                    ->schema([
                                        Forms\Components\TextInput::make('openai_api_key')
                                            ->label('API Key')
                                            ->password()
                                            ->revealable()
                                            ->autocomplete(false)
                                            ->placeholder('sk-...')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('نموذج الذكاء الاصطناعي')
                                    ->description('يُطبَّق على كل طلبات الذكاء الاصطناعي. حدود الاستهلاك لكل خطة تُضبط من صفحة «خطط الاشتراك».')
                                    ->schema([
                                        Forms\Components\Select::make('ai_model')
                                            ->label('النموذج')
                                            ->options([
                                                'gpt-4o'      => 'GPT-4o',
                                                'gpt-4o-mini' => 'GPT-4o mini (أرخص)',
                                                'gpt-4-turbo' => 'GPT-4 Turbo',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('max_tokens')->label('أقصى توكنز للطلب')->numeric()->minValue(256)->maxValue(8000)->required(),
                                        Forms\Components\TextInput::make('temperature')->label('درجة الحرارة (0–1)')->numeric()->step(0.1)->minValue(0)->maxValue(1)->required(),
                                    ])->columns(3),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('saveAi')
                                        ->label('حفظ إعدادات الذكاء الاصطناعي')
                                        ->icon('heroicon-o-check')
                                        ->action('saveAi'),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('addons.messaging'))
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                // ── WhatsApp ──────────────────────────────
                                Forms\Components\Section::make(__('addons.whatsapp_from'))
                                    ->schema([
                                        Forms\Components\Select::make('messaging.whatsapp_provider')
                                            ->label(__('addons.whatsapp_provider'))
                                            ->options([
                                                'twilio'     => __('addons.provider_twilio'),
                                                'meta_cloud' => __('addons.provider_meta'),
                                            ])
                                            ->default('twilio')->live()->required()->columnSpanFull(),

                                        // Twilio (shared creds with SMS)
                                        Forms\Components\TextInput::make('messaging.twilio.sid')->label(__('addons.sid'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.whatsapp_provider') === 'twilio')
                                            ->autocomplete(false)->placeholder('ACxxxxxxxx'),
                                        Forms\Components\TextInput::make('messaging.twilio.token')->label(__('addons.token'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.whatsapp_provider') === 'twilio')
                                            ->password()->revealable()->autocomplete(false),
                                        Forms\Components\TextInput::make('messaging.twilio.whatsapp_from')->label(__('addons.whatsapp_from'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.whatsapp_provider') === 'twilio')
                                            ->placeholder('+14155238886')->extraInputAttributes(['dir' => 'ltr']),

                                        // Meta Cloud
                                        Forms\Components\TextInput::make('messaging.meta.token')->label(__('addons.meta_token'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.whatsapp_provider') === 'meta_cloud')
                                            ->password()->revealable()->autocomplete(false)->columnSpanFull(),
                                        Forms\Components\TextInput::make('messaging.meta.phone_id')->label(__('addons.meta_phone_id'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.whatsapp_provider') === 'meta_cloud')
                                            ->extraInputAttributes(['dir' => 'ltr']),
                                    ])->columns(2)
                                    ->footerActions([
                                        Forms\Components\Actions\Action::make('saveMessagingWa')->label(__('addons.saved'))->icon('heroicon-o-check')->action('saveMessaging'),
                                        Forms\Components\Actions\Action::make('testWhatsapp')->label(__('addons.test_whatsapp'))->color('gray')->icon('heroicon-o-signal')->action('testWhatsapp'),
                                    ]),

                                // ── SMS ───────────────────────────────────
                                Forms\Components\Section::make(__('addons.sms_provider'))
                                    ->schema([
                                        Forms\Components\Select::make('messaging.sms_provider')
                                            ->label(__('addons.sms_provider'))
                                            ->options([
                                                'twilio'     => __('addons.provider_twilio'),
                                                'egypt_http' => __('addons.provider_egypt'),
                                                'vonage'     => __('addons.provider_vonage'),
                                            ])
                                            ->default('twilio')->live()->required()->columnSpanFull(),

                                        // Twilio
                                        Forms\Components\TextInput::make('messaging.twilio.sms_from')->label(__('addons.sms_from'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'twilio')
                                            ->placeholder('+1xxxxxxxxxx')->extraInputAttributes(['dir' => 'ltr'])
                                            ->helperText('يستخدم نفس SID/Token الخاص بواتساب Twilio.'),

                                        // Egypt HTTP
                                        Forms\Components\TextInput::make('messaging.egypt_http.url')->label(__('addons.eg_url'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'egypt_http')
                                            ->extraInputAttributes(['dir' => 'ltr'])->columnSpanFull(),
                                        Forms\Components\Select::make('messaging.egypt_http.method')->label(__('addons.eg_method'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'egypt_http')
                                            ->options(['get' => 'GET', 'post' => 'POST'])->default('get'),
                                        Forms\Components\TextInput::make('messaging.egypt_http.sender')->label(__('addons.eg_sender'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'egypt_http'),
                                        Forms\Components\TextInput::make('messaging.egypt_http.username')->label(__('addons.eg_username'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'egypt_http')->autocomplete(false),
                                        Forms\Components\TextInput::make('messaging.egypt_http.password')->label(__('addons.eg_password'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'egypt_http')
                                            ->password()->revealable()->autocomplete(false),
                                        Forms\Components\Select::make('messaging.egypt_http.lang')->label(__('addons.eg_lang'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'egypt_http')
                                            ->options(['1' => 'English', '2' => 'العربية'])->default('2'),

                                        // Vonage
                                        Forms\Components\TextInput::make('messaging.vonage.key')->label(__('addons.vonage_key'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'vonage')->autocomplete(false),
                                        Forms\Components\TextInput::make('messaging.vonage.secret')->label(__('addons.vonage_secret'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'vonage')
                                            ->password()->revealable()->autocomplete(false),
                                        Forms\Components\TextInput::make('messaging.vonage.from')->label(__('addons.vonage_from'))
                                            ->visible(fn (Forms\Get $get) => $get('messaging.sms_provider') === 'vonage')
                                            ->extraInputAttributes(['dir' => 'ltr']),
                                    ])->columns(2)
                                    ->footerActions([
                                        Forms\Components\Actions\Action::make('saveMessagingSms')->label(__('addons.saved'))->icon('heroicon-o-check')->action('saveMessaging'),
                                        Forms\Components\Actions\Action::make('testSms')->label(__('addons.test_sms'))->color('gray')->icon('heroicon-o-signal')->action('testSms'),
                                    ]),

                                // ── Telegram (free) ───────────────────────
                                Forms\Components\Section::make(__('addons.telegram'))
                                    ->description(__('addons.telegram_desc'))
                                    ->schema([
                                        Forms\Components\Toggle::make('messaging.telegram_enabled')->label(__('addons.telegram_enable'))->columnSpanFull(),
                                        Forms\Components\TextInput::make('messaging.telegram.bot_token')->label(__('addons.tg_bot_token'))
                                            ->password()->revealable()->autocomplete(false)->extraInputAttributes(['dir' => 'ltr']),
                                        Forms\Components\TextInput::make('messaging.telegram.bot_username')->label(__('addons.tg_bot_username'))
                                            ->placeholder('mizan_bot')->extraInputAttributes(['dir' => 'ltr']),
                                        Forms\Components\TextInput::make('messaging.telegram.webhook_secret')->label(__('addons.tg_webhook_secret'))
                                            ->extraInputAttributes(['dir' => 'ltr'])->columnSpanFull(),
                                    ])->columns(2)
                                    ->footerActions([
                                        Forms\Components\Actions\Action::make('saveMessagingTg')->label(__('addons.saved'))->icon('heroicon-o-check')->action('saveMessaging'),
                                        Forms\Components\Actions\Action::make('registerTelegramWebhook')->label(__('addons.tg_register_webhook'))->color('gray')->icon('heroicon-o-link')->action('registerTelegramWebhook'),
                                        Forms\Components\Actions\Action::make('testTelegram')->label(__('addons.test_telegram'))->color('gray')->icon('heroicon-o-signal')->action('testTelegram'),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    // ── Save handlers ──────────────────────────────────────────────────────────

    public function saveHomepage(): void
    {
        $s = $this->form->getState();

        PlatformSetting::put([
            'homepage' => [
                'show_live_bar'      => (bool) ($s['show_live_bar'] ?? true),
                'show_before_after'  => (bool) ($s['show_before_after'] ?? true),
                'show_activity_feed' => (bool) ($s['show_activity_feed'] ?? true),
                'show_trust_badges'  => (bool) ($s['show_trust_badges'] ?? true),
            ],
        ]);

        Notification::make()->title('تم حفظ إعدادات الصفحة الرئيسية')->success()->send();
    }

    public function saveSystem(): void
    {
        $s = $this->form->getState();

        PlatformSetting::put([
            'announcement' => [
                'enabled' => (bool) ($s['announcement_enabled'] ?? false),
                'color'   => $s['announcement_color'] ?? 'gold',
                'text_ar' => $s['announcement_text_ar'] ?? '',
                'text_en' => $s['announcement_text_en'] ?? '',
            ],
            'developer' => [
                'name'     => $s['developer_name'] ?? 'Mohamed Shahin',
                'linkedin' => $s['developer_linkedin'] ?? '',
            ],
            'contact' => [
                'map_embed_src' => $s['platform_map_src'] ?? '',
            ],
        ]);

        Notification::make()
            ->title('تم حفظ إعدادات النظام')
            ->body('يتطلب بعض التغييرات إعادة تشغيل السيرفر.')
            ->warning()
            ->send();
    }

    public function clearCache(): void
    {
        Artisan::call('optimize:clear');

        Notification::make()
            ->title('تم مسح الكاش بنجاح')
            ->success()
            ->send();
    }

    public function saveBilling(): void
    {
        $state = $this->form->getState();

        PlatformSetting::singleton()->update([
            'billing_gateway'   => $state['billing_gateway'] ?? 'paymob',
            'billing_test_mode' => $state['billing_test_mode'] ?? true,
            'billing_config'    => array_filter($state['billing_config'] ?? [], fn ($v) => $v !== null && $v !== ''),
        ]);

        Notification::make()->title('تم حفظ إعدادات بوابة الدفع بنجاح')->success()->send();
    }

    public function testConnection(): void
    {
        $this->saveBilling();

        try {
            $ok = PlatformBillingService::gateway()->testConnection();

            Notification::make()
                ->title($ok ? 'نجح الاتصال بالبوابة ✓' : 'فشل الاتصال — تحقق من المفاتيح')
                ->{$ok ? 'success' : 'danger'}()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()->title('تعذّر الاتصال')->body($e->getMessage())->danger()->send();
        }
    }

    public function saveMail(): void
    {
        $state = $this->form->getState();

        PlatformSetting::singleton()->update([
            'mail_config' => array_filter($state['mail'] ?? [], fn ($v) => $v !== null && $v !== ''),
        ]);

        PlatformSetting::put([
            'otp' => [
                'length'       => (int) ($state['otp_length'] ?? 6),
                'ttl_minutes'  => (int) ($state['otp_ttl_minutes'] ?? 15),
                'max_attempts' => (int) ($state['otp_max_attempts'] ?? 5),
            ],
            'security' => [
                'email_verification_enabled' => (bool) ($state['email_verification_enabled'] ?? false),
            ],
        ]);

        Notification::make()->title('تم حفظ إعدادات البريد بنجاح')->success()->send();
    }

    public function sendTest(string $recipient): void
    {
        $this->saveMail();

        $mail = PlatformSetting::mail();
        if (empty($mail['host'])) {
            Notification::make()->title('اضبط خادم البريد أولاً')->danger()->send();
            return;
        }

        try {
            Config::set('mail.mailers.smtp', array_filter([
                'transport'  => 'smtp',
                'host'       => $mail['host'] ?? null,
                'port'       => $mail['port'] ?? 587,
                'username'   => $mail['username'] ?? null,
                'password'   => $mail['password'] ?? null,
                'encryption' => $mail['encryption'] ?? 'tls',
            ], fn ($v) => $v !== null));
            Config::set('mail.default', 'smtp');
            if (! empty($mail['from_address'])) {
                Config::set('mail.from.address', $mail['from_address']);
                Config::set('mail.from.name', $mail['from_name'] ?? 'ميزان');
            }

            Mail::raw('رسالة اختبار من منصة ميزان — إعدادات البريد تعمل بنجاح ✓', function ($m) use ($recipient) {
                $m->to($recipient)->subject('اختبار بريد ميزان');
            });

            Notification::make()->title('تم إرسال إيميل اختبار إلى ' . $recipient)->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('فشل الإرسال')->body($e->getMessage())->danger()->send();
        }
    }

    public function saveSecurity(): void
    {
        $s = $this->form->getState();

        PlatformSetting::put([
            'security' => [
                'rate' => [
                    'login'    => (int) $s['rate_login'],
                    'register' => (int) $s['rate_register'],
                    'contact'  => (int) $s['rate_contact'],
                    'otp'      => (int) $s['rate_otp'],
                    'uploads'  => (int) $s['rate_uploads'],
                    'ai'       => (int) $s['rate_ai'],
                    'api'      => (int) $s['rate_api'],
                ],
            ],
            'media' => [
                'max_upload_kb' => (int) $s['max_upload_kb'],
                'avatar_max_kb' => (int) $s['avatar_max_kb'],
            ],
        ]);

        Notification::make()->title('تم حفظ إعدادات الأمان والوسائط')->success()->send();
    }

    public function saveAi(): void
    {
        $s = $this->form->getState();

        $row = PlatformSetting::singleton();

        if (filled($s['openai_api_key'])) {
            $row->update(['openai_api_key' => $s['openai_api_key']]);
        }

        PlatformSetting::put([
            'ai' => [
                'model'       => $s['ai_model'],
                'max_tokens'  => (int) $s['max_tokens'],
                'temperature' => (float) $s['temperature'],
            ],
        ]);

        Notification::make()->title('تم حفظ إعدادات الذكاء الاصطناعي')->success()->send();
    }

    public function saveMessaging(): void
    {
        $state = $this->form->getState();

        // Keep the nested structure; only strip empty leaf strings.
        $messaging = $this->pruneEmpty($state['messaging'] ?? []);

        PlatformSetting::singleton()->update(['messaging_config' => $messaging]);

        Notification::make()->title(__('addons.saved'))->success()->send();
    }

    private function pruneEmpty(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->pruneEmpty($value);
            } elseif ($value === null || $value === '') {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public function testSms(): void
    {
        $this->saveMessaging();
        $gateway = \App\Services\Messaging\MessagingService::resolveSms();
        $this->reportTest($gateway?->test() ?? false, $gateway !== null);
    }

    public function testWhatsapp(): void
    {
        $this->saveMessaging();
        $gateway = \App\Services\Messaging\MessagingService::resolveWhatsapp();
        $this->reportTest($gateway?->test() ?? false, $gateway !== null);
    }

    public function testTelegram(): void
    {
        $this->saveMessaging();
        $gateway = \App\Services\Messaging\MessagingService::resolveTelegram();
        $this->reportTest($gateway?->test() ?? false, $gateway !== null);
    }

    public function registerTelegramWebhook(): void
    {
        $this->saveMessaging();

        $gateway = \App\Services\Messaging\MessagingService::resolveTelegram();
        $secret  = PlatformSetting::messaging()['telegram']['webhook_secret'] ?? '';

        if (! $gateway || ! $secret) {
            Notification::make()->title(__('addons.not_configured'))->danger()->send();
            return;
        }

        $result = $gateway->setWebhook(route('telegram.webhook', ['secret' => $secret]));

        ($result['success'] ?? false)
            ? Notification::make()->title(__('addons.tg_webhook_ok'))->success()->send()
            : Notification::make()->title(__('addons.tg_webhook_failed'))->body($result['message'] ?? '')->danger()->send();
    }

    private function reportTest(bool $ok, bool $configured): void
    {
        if (! $configured) {
            Notification::make()->title(__('addons.not_configured'))->danger()->send();
            return;
        }

        $ok
            ? Notification::make()->title(__('addons.connection_ok'))->success()->send()
            : Notification::make()->title(__('addons.connection_failed'))->danger()->send();
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
