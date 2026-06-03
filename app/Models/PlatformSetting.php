<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('platform_settings_data');
            \Illuminate\Support\Facades\Cache::forget('platform_settings_mail');
        });
    }

    protected function casts(): array
    {
        return [
            'data'              => 'array',
            'billing_config'    => 'encrypted:array',
            'billing_test_mode' => 'boolean',
            'mail_config'       => 'encrypted:array',
            'messaging_config'  => 'encrypted:array',
            'openai_api_key'    => 'encrypted',
        ];
    }

    /**
     * Read a setting from the singleton `data` JSON using dot-notation,
     * with a safe default. Cached for 60s to avoid a query per request.
     * Safe to call before the table exists (returns default).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $data = \Illuminate\Support\Facades\Cache::remember(
                'platform_settings_data',
                60,
                fn () => static::query()->value('data') ?? []
            );
        } catch (\Throwable) {
            return $default;
        }

        return data_get($data, $key, $default);
    }

    /**
     * Merge values into the singleton `data` JSON and clear the cache.
     */
    public static function put(array $values): void
    {
        $row  = static::singleton();
        $data = array_replace_recursive($row->data ?? [], $values);
        $row->update(['data' => $data]);

        \Illuminate\Support\Facades\Cache::forget('platform_settings_data');
    }

    /**
     * Resolve the platform mail (SMTP) settings — dashboard-managed (encrypted),
     * falling back to config/.env when not set.
     */
    /**
     * Resolve the OpenAI API key — prefers the dashboard-managed value (DB),
     * falling back to config/services (.env) when not set.
     */
    public static function openaiKey(): string
    {
        $row = static::query()->first();

        return $row?->openai_api_key ?: config('services.openai.api_key', '');
    }

    public static function mail(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('platform_settings_mail', 60, function () {
            $row = static::query()->first();
            return $row?->mail_config ?: [];
        });
    }

    /**
     * Resolve the platform messaging (SMS/WhatsApp) settings — dashboard-managed
     * (encrypted). Shape: ['provider' => 'twilio', 'sid' => ..., 'token' => ...,
     * 'sms_from' => ..., 'whatsapp_from' => ...].
     */
    public static function messaging(): array
    {
        $row = static::query()->first();

        return $row?->messaging_config ?: [];
    }

    /**
     * Resolve the platform billing settings — prefers the dashboard-managed
     * values (DB), falling back to config/services (.env) when not set.
     *
     * @return array{gateway:string, config:array, test_mode:bool}
     */
    public static function billing(): array
    {
        $row = static::query()->first();

        $gateway = $row?->billing_gateway ?: config('services.platform_billing.gateway', 'paymob');
        $config  = $row?->billing_config ?: (config("services.platform_billing.$gateway", []) ?: []);

        return [
            'gateway'   => $gateway,
            'config'    => array_filter($config, fn ($v) => $v !== null && $v !== ''),
            'test_mode' => $row?->billing_test_mode ?? true,
        ];
    }

    /**
     * Get the singleton settings row (creates it if missing),
     * merged with the platform defaults.
     */
    public static function current(): array
    {
        $row = static::query()->first();

        return array_replace_recursive(static::defaults(), $row?->data ?? []);
    }

    /**
     * Get (or create) the singleton row for editing.
     */
    public static function singleton(): self
    {
        return static::query()->firstOrCreate([], ['data' => static::defaults()]);
    }

    public static function defaults(): array
    {
        return [
            'brand' => [
                'name_ar'   => 'ميزان',
                'name_en'   => 'Mizan',
                'logo_path' => null,
            ],
            'hero' => [
                'heading_ar'  => 'منصة ميزان لإدارة مكاتب المحاماة',
                'heading_en'  => 'Mizan — Law Office Management Platform',
                'subtitle_ar' => 'نظام متكامل لإدارة القضايا والجلسات والعملاء والفواتير والوثائق — مع بوابة عملاء وذكاء اصطناعي. ابدأ تجربتك المجانية لمدة شهر كامل.',
                'subtitle_en' => 'A complete system to manage cases, hearings, clients, invoices, and documents — with a client portal and AI. Start your free one-month trial.',
                'cta_ar'      => 'ابدأ مجاناً',
                'cta_en'      => 'Start Free',
            ],
            'stats' => [
                ['value' => 5000, 'suffix' => '+', 'label_ar' => 'قضية مُدارة',   'label_en' => 'Cases Managed'],
                ['value' => 300,  'suffix' => '+', 'label_ar' => 'مكتب محاماة',   'label_en' => 'Law Offices'],
                ['value' => 99,   'suffix' => '%', 'label_ar' => 'رضا العملاء',   'label_en' => 'Satisfaction'],
                ['value' => 24,   'suffix' => '/7', 'label_ar' => 'دعم متواصل',   'label_en' => 'Support'],
            ],
            'features' => [
                ['icon' => 'scale',     'title_ar' => 'إدارة القضايا',        'title_en' => 'Case Management',   'desc_ar' => 'تتبّع كل قضية من الفتح حتى الحكم مع الجلسات والوثائق والفريق.',         'desc_en' => 'Track every case from opening to verdict with hearings, documents, and team.'],
                ['icon' => 'calendar',  'title_ar' => 'الجلسات والمواعيد',    'title_en' => 'Hearings & Calendar','desc_ar' => 'تقويم جلسات ذكي مع تذكيرات تلقائية حتى لا تفوتك جلسة.',                  'desc_en' => 'Smart hearing calendar with automatic reminders so you never miss a session.'],
                ['icon' => 'users',     'title_ar' => 'إدارة العملاء',        'title_en' => 'Client Management', 'desc_ar' => 'ملف كامل لكل عميل مع قضاياه وفواتيره ومستنداته في مكان واحد.',           'desc_en' => 'A complete profile for each client with cases, invoices, and documents.'],
                ['icon' => 'document',  'title_ar' => 'الوثائق والقوالب',     'title_en' => 'Documents & Templates','desc_ar' => 'أرشفة المستندات وإنشاء عقود ومذكرات جاهزة من قوالب بنقرة واحدة.',     'desc_en' => 'Archive documents and generate contracts from templates in one click.'],
                ['icon' => 'cash',      'title_ar' => 'الفواتير والمدفوعات',  'title_en' => 'Invoices & Payments','desc_ar' => 'فواتير احترافية وتحصيل أونلاين عبر بوابات دفع متعددة.',                'desc_en' => 'Professional invoices and online collection via multiple gateways.'],
                ['icon' => 'sparkles',  'title_ar' => 'الذكاء الاصطناعي',     'title_en' => 'AI Assistant',      'desc_ar' => 'تلخيص الوثائق وتحليل العقود واقتراح الاستراتيجيات القانونية.',           'desc_en' => 'Summarize documents, analyze contracts, and suggest legal strategies.'],
            ],
            'why_us' => [
                ['icon' => 'shield',  'title_ar' => 'أمان وخصوصية',   'title_en' => 'Security & Privacy',  'desc_ar' => 'بياناتك مشفّرة ومحمية بأعلى المعايير، وكل مكتب معزول تماماً عن غيره.',  'desc_en' => 'Your data is encrypted and protected to the highest standards, fully isolated per office.'],
                ['icon' => 'bolt',    'title_ar' => 'سهولة وسرعة',    'title_en' => 'Fast & Simple',       'desc_ar' => 'واجهة عربية أنيقة وسريعة، تبدأ خلال دقائق بدون تعقيد أو تدريب طويل.',     'desc_en' => 'An elegant, fast Arabic interface — get started in minutes, no long training.'],
                ['icon' => 'headset', 'title_ar' => 'دعم متواصل',     'title_en' => 'Dedicated Support',   'desc_ar' => 'فريق دعم جاهز لمساعدتك في أي وقت لضمان عمل مكتبك بسلاسة.',                'desc_en' => 'A support team ready to help anytime to keep your office running smoothly.'],
                ['icon' => 'refresh', 'title_ar' => 'تحديثات مستمرة', 'title_en' => 'Continuous Updates',  'desc_ar' => 'نطوّر المنصة باستمرار ونضيف مميزات جديدة دون أي تكلفة إضافية.',           'desc_en' => 'We continuously improve the platform and add features at no extra cost.'],
            ],
            'contact' => [
                'phone'      => '+20 100 000 0000',
                'email'      => 'info@mizan.com',
                'whatsapp'   => '201000000000',
                'address_ar' => 'مصر — القاهرة',
                'address_en' => 'Egypt — Cairo',
                'facebook'   => null,
                'twitter_x'  => null,
                'instagram'  => null,
                'linkedin'   => null,
            ],
            'tracking' => [
                'ga4_id'               => null,
                'gtm_id'               => null,
                'search_console_token' => null,
            ],
        ];
    }
}
