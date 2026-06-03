<?php

namespace App\Filament\Pages;

use App\Models\Office;
use App\Services\DomainVerificationService;
use App\Services\ImageProcessingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class LandingSettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'تخصيص الموقع';
    protected static ?string $title           = 'تخصيص الصفحة التعريفية';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.landing-settings';

    public ?array $data = [];

    // Domain fields — stored directly on offices table, not inside settings JSON
    public string  $customDomain      = '';
    public ?string $domainVerifyToken = null;
    public ?string $domainVerifiedAt  = null;

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('office_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('office_admin') ?? false;
    }

    public function mount(): void
    {
        $office   = Office::query()->find(Auth::user()?->office_id ?? 0);
        $settings = array_replace_recursive(
            app(\App\Http\Controllers\LandingController::class)->getDefaultSettings(),
            $office?->settings ?? []
        );
        $this->form->fill($settings);

        // Load domain fields from the office record itself
        if ($office) {
            $this->customDomain      = $office->custom_domain ?? '';
            $this->domainVerifyToken = $office->domain_verify_token;
            $this->domainVerifiedAt  = $office->domain_verified_at?->format('Y-m-d H:i');
        }
    }

    public function form(Form $form): Form
    {
        $iconOptions = [
            'scale'     => '⚖️ عامر — قضايا مدنية',
            'building'  => '🏢 مبنى — قانون تجاري',
            'users'     => '👥 أشخاص — قضايا أسرة',
            'shield'    => '🛡️ درع — قضايا جنائية',
            'briefcase' => '💼 حقيبة — قانون عمل',
            'home'      => '🏠 منزل — عقارات',
            'star'      => '⭐ نجمة',
            'lock'      => '🔒 سرية',
            'clock'     => '🕐 ساعة',
            'document'  => '📄 وثيقة',
            'globe'     => '🌐 عالمي',
            'money'     => '💰 مالي',
            'lightbulb' => '💡 خبرة',
        ];

        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([

                        // ── Tab 1: Branding ────────────────────────────────────────────
                        Forms\Components\Tabs\Tab::make('الهوية البصرية')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\FileUpload::make('branding.logo_path')
                                    ->label('شعار المكتب (Logo)')
                                    ->helperText('PNG أو JPG — حد أقصى 5MB — سيُحوَّل تلقائياً لـ WebP (400×400)')
                                    ->disk('public')
                                    ->directory('logos')
                                    ->maxSize(5120)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->deletable(true)
                                    ->hintActions([
                                        Forms\Components\Actions\Action::make('edit_logo')
                                            ->label('تعديل الصورة')
                                            ->icon('heroicon-o-pencil-square')
                                            ->color('warning')
                                            ->extraAttributes(fn() => $this->editorAttrs('branding.logo_path'))
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('branding.favicon_path')
                                    ->label('Favicon (أيقونة التبويب)')
                                    ->helperText('PNG أو ICO أو SVG — يُفضَّل 512×512 px — يظهر في تبويب المتصفح بجانب اسم المكتب')
                                    ->disk('public')
                                    ->directory('favicons')
                                    ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/svg+xml', 'image/webp'])
                                    ->maxSize(512)
                                    ->deletable(true)
                                    ->columnSpanFull(),
                                Forms\Components\ColorPicker::make('branding.primary_color')
                                    ->label('اللون الأساسي (Navy)')
                                    ->helperText('الافتراضي: #1E3A5F — اضغط على المربع لاختيار لون جديد')
                                    ->default('#1E3A5F'),
                                Forms\Components\ColorPicker::make('branding.accent_color')
                                    ->label('لون التمييز (Gold)')
                                    ->helperText('الافتراضي: #C9A84C — اضغط على المربع لاختيار لون جديد')
                                    ->default('#C9A84C'),
                            ])->columns(2),

                        // ── Tab 2: Hero ────────────────────────────────────────────────
                        Forms\Components\Tabs\Tab::make('القسم الرئيسي')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\FileUpload::make('hero.image_path')
                                    ->label('صورة الهيرو (صورة المحامي / مقر المكتب)')
                                    ->helperText('JPG أو PNG — حد أقصى 10MB — ستُعالج تلقائياً: crop 1200×600 WebP جودة 85%')
                                    ->disk('public')
                                    ->directory('hero')
                                    ->maxSize(10240)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->deletable(true)
                                    ->hintActions([
                                        Forms\Components\Actions\Action::make('edit_hero')
                                            ->label('تعديل الصورة')
                                            ->icon('heroicon-o-pencil-square')
                                            ->color('warning')
                                            ->extraAttributes(fn() => $this->editorAttrs('hero.image_path'))
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('hero.heading_ar')
                                    ->label('العنوان الرئيسي (عربي)')
                                    ->required(),
                                Forms\Components\TextInput::make('hero.heading_en')
                                    ->label('العنوان الرئيسي (English)'),
                                Forms\Components\Textarea::make('hero.subtitle_ar')
                                    ->label('الوصف التعريفي (عربي)')
                                    ->rows(3),
                                Forms\Components\Textarea::make('hero.subtitle_en')
                                    ->label('الوصف التعريفي (English)')
                                    ->rows(3),
                                Forms\Components\TextInput::make('hero.founded_year')
                                    ->label('سنة التأسيس')
                                    ->default('1995'),
                                Forms\Components\Section::make('إحصائيات الهيرو (3 أرقام)')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero.stat_cases')
                                            ->label('عدد القضايا الناجحة')
                                            ->numeric()->default(500),
                                        Forms\Components\TextInput::make('hero.stat_years')
                                            ->label('سنوات الخبرة')
                                            ->numeric()->default(25),
                                        Forms\Components\TextInput::make('hero.stat_satisfaction')
                                            ->label('نسبة رضا العملاء %')
                                            ->numeric()->default(98),
                                    ])->columns(3)->collapsed(),
                            ])->columns(2),

                        // ── Tab 3: Services + Team + Testimonials ──────────────────────
                        Forms\Components\Tabs\Tab::make('الخدمات والفريق')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\Section::make('الخدمات القانونية')
                                    ->schema([
                                        Forms\Components\Repeater::make('services')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\TextInput::make('title_ar')->label('الاسم عربي')->required(),
                                                Forms\Components\TextInput::make('title_en')->label('الاسم English'),
                                                Forms\Components\Select::make('icon')
                                                    ->label('الأيقونة')
                                                    ->options($iconOptions)
                                                    ->default('scale'),
                                                Forms\Components\Textarea::make('desc_ar')->label('الوصف عربي')->rows(2),
                                                Forms\Components\Textarea::make('desc_en')->label('الوصف English')->rows(2),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('+ إضافة خدمة')
                                            ->collapsible()
                                            ->defaultItems(0),
                                    ]),

                                Forms\Components\Section::make('أعضاء الفريق')
                                    ->schema([
                                        Forms\Components\Repeater::make('team')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\FileUpload::make('photo')
                                                    ->label('صورة العضو')
                                                    ->helperText('JPG أو PNG — حد أقصى 5MB — ستُعالج تلقائياً 400×400 WebP')
                                                    ->disk('public')
                                                    ->directory('team')
                                                    ->maxSize(5120)
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                    ->deletable(true)
                                                    ->hintActions([
                                                        Forms\Components\Actions\Action::make('edit_team_photo')
                                                            ->label('تعديل الصورة')
                                                            ->icon('heroicon-o-pencil-square')
                                                            ->color('warning')
                                                            ->extraAttributes(fn() => $this->editorAttrs('team.0.photo'))
                                                    ])
                                                    ->columnSpanFull(),
                                                Forms\Components\TextInput::make('name_ar')->label('الاسم عربي')->required(),
                                                Forms\Components\TextInput::make('name_en')->label('الاسم English'),
                                                Forms\Components\TextInput::make('role_ar')->label('المنصب عربي'),
                                                Forms\Components\TextInput::make('role_en')->label('المنصب English'),
                                                Forms\Components\TextInput::make('initials')->label('الحرف الأول (لو مفيش صورة)')->maxLength(2),
                                                Forms\Components\Select::make('color')
                                                    ->label('لون الكارد (لو مفيش صورة)')
                                                    ->options([
                                                        'bg-navy'       => 'كحلي داكن',
                                                        'bg-navy-light' => 'كحلي فاتح',
                                                        'bg-gold'       => 'ذهبي',
                                                    ])
                                                    ->default('bg-navy'),
                                                Forms\Components\Textarea::make('bio_ar')->label('النبذة عربي')->rows(2),
                                                Forms\Components\Textarea::make('bio_en')->label('النبذة English')->rows(2),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('+ إضافة عضو')
                                            ->collapsible()
                                            ->defaultItems(0),
                                    ])->collapsed(),

                                Forms\Components\Section::make('آراء العملاء')
                                    ->schema([
                                        Forms\Components\Repeater::make('testimonials')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Textarea::make('quote_ar')->label('التقييم عربي')->rows(3)->required(),
                                                Forms\Components\Textarea::make('quote_en')->label('التقييم English')->rows(3),
                                                Forms\Components\TextInput::make('name_ar')->label('اسم العميل عربي'),
                                                Forms\Components\TextInput::make('name_en')->label('اسم العميل English'),
                                                Forms\Components\TextInput::make('role_ar')->label('المنصب عربي'),
                                                Forms\Components\TextInput::make('role_en')->label('المنصب English'),
                                                Forms\Components\TextInput::make('initials')->label('الحرف الأول')->maxLength(2),
                                                Forms\Components\Select::make('rating')
                                                    ->label('عدد النجوم')
                                                    ->options([5 => '⭐⭐⭐⭐⭐', 4 => '⭐⭐⭐⭐', 3 => '⭐⭐⭐'])
                                                    ->default(5),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('+ إضافة تقييم')
                                            ->collapsible()
                                            ->defaultItems(0),
                                    ])->collapsed(),
                            ]),

                        // ── Tab 4: Stats ───────────────────────────────────────────────
                        Forms\Components\Tabs\Tab::make('الإحصائيات')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Repeater::make('stats')
                                    ->label('الأرقام والإحصائيات (قسم الأرقام المنفصل)')
                                    ->schema([
                                        Forms\Components\TextInput::make('value')->label('الرقم')->numeric()->required(),
                                        Forms\Components\TextInput::make('suffix')->label('اللاحقة (+، %)')->maxLength(2)->default('+'),
                                        Forms\Components\TextInput::make('label_ar')->label('التسمية عربي')->required(),
                                        Forms\Components\TextInput::make('label_en')->label('التسمية English'),
                                    ])
                                    ->columns(4)
                                    ->addActionLabel('+ إضافة إحصائية')
                                    ->collapsible()
                                    ->defaultItems(0),
                            ]),

                        // ── Tab 5: Why Us ──────────────────────────────────────────────
                        Forms\Components\Tabs\Tab::make('لماذا نحن')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Forms\Components\Repeater::make('why_us')
                                    ->label('مزايا المكتب')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ar')->label('العنوان عربي')->required(),
                                        Forms\Components\TextInput::make('title_en')->label('العنوان English'),
                                        Forms\Components\Select::make('icon')
                                            ->label('الأيقونة')
                                            ->options([
                                                'lightbulb' => '💡 خبرة',
                                                'lock'      => '🔒 سرية',
                                                'clock'     => '🕐 متاحون',
                                                'star'      => '⭐ تميز',
                                                'shield'    => '🛡️ حماية',
                                                'users'     => '👥 فريق',
                                            ])
                                            ->default('lightbulb'),
                                        Forms\Components\Textarea::make('desc_ar')->label('الوصف عربي')->rows(2),
                                        Forms\Components\Textarea::make('desc_en')->label('الوصف English')->rows(2),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('+ إضافة ميزة')
                                    ->collapsible()
                                    ->defaultItems(0),
                            ]),

                        // ── Tab 6: Contact ─────────────────────────────────────────────
                        Forms\Components\Tabs\Tab::make('التواصل')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Section::make('بيانات التواصل')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact.phone')
                                            ->label('رقم الهاتف الأول')->tel(),
                                        Forms\Components\TextInput::make('contact.phone2')
                                            ->label('رقم الهاتف الثاني')->tel(),
                                        Forms\Components\TextInput::make('contact.email')
                                            ->label('البريد الإلكتروني')->email(),
                                        Forms\Components\TextInput::make('contact.whatsapp')
                                            ->label('واتساب'),
                                        Forms\Components\TextInput::make('contact.address_ar')
                                            ->label('العنوان (عربي)'),
                                        Forms\Components\TextInput::make('contact.address_en')
                                            ->label('العنوان (English)'),
                                        Forms\Components\TextInput::make('contact.working_hours_ar')
                                            ->label('ساعات العمل (عربي)')
                                            ->placeholder('الأحد — الخميس: ٩ ص — ٦ م'),
                                        Forms\Components\TextInput::make('contact.working_hours_en')
                                            ->label('ساعات العمل (English)')
                                            ->placeholder('Sunday — Thursday: 9 AM — 6 PM'),
                                    ])->columns(2),

                                Forms\Components\Section::make('الموقع الجغرافي (للبحث المحلي)')
                                    ->description('يُستخدم في نتائج جوجل المحلية — أدخل إحداثيات مكتبك من Google Maps.')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact.latitude')
                                            ->label('خط العرض (Latitude)')
                                            ->placeholder('30.0444')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->numeric(),
                                        Forms\Components\TextInput::make('contact.longitude')
                                            ->label('خط الطول (Longitude)')
                                            ->placeholder('31.2357')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->numeric(),
                                    ])->columns(2)->collapsed(),

                                Forms\Components\Section::make('السوشيال ميديا')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact.facebook')
                                            ->label('Facebook')->url()
                                            ->placeholder('https://facebook.com/...'),
                                        Forms\Components\TextInput::make('contact.twitter_x')
                                            ->label('X (Twitter)')->url()
                                            ->placeholder('https://x.com/...'),
                                        Forms\Components\TextInput::make('contact.instagram')
                                            ->label('Instagram')->url()
                                            ->placeholder('https://instagram.com/...'),
                                        Forms\Components\TextInput::make('contact.linkedin')
                                            ->label('LinkedIn')->url()
                                            ->placeholder('https://linkedin.com/...'),
                                        Forms\Components\TextInput::make('contact.youtube')
                                            ->label('YouTube')->url()
                                            ->placeholder('https://youtube.com/...'),
                                        Forms\Components\TextInput::make('contact.tiktok')
                                            ->label('TikTok')->url()
                                            ->placeholder('https://tiktok.com/...'),
                                    ])->columns(2)->collapsed(),
                            ]),

                        // ── Tab 7: SEO ─────────────────────────────────────────────────
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Section::make('محركات البحث والمشاركة')
                                    ->schema([
                                        Forms\Components\TextInput::make('seo.meta_title')
                                            ->label('عنوان الصفحة (Meta Title)')
                                            ->helperText('اتركه فارغاً لاستخدام اسم المكتب تلقائياً')
                                            ->maxLength(70)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('seo.meta_description')
                                            ->label('الوصف (Meta Description)')
                                            ->helperText('150-160 حرف — يظهر في نتائج البحث')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('seo.meta_keywords')
                                            ->label('الكلمات المفتاحية')
                                            ->helperText('مفصولة بفواصل: محامي، قانون، قضايا')
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('seo.og_image_path')
                                            ->label('صورة المشاركة (OG Image)')
                                            ->helperText('تظهر عند مشاركة الرابط على واتساب وتويتر وفيسبوك — 1200×630 px مُفضَّل')
                                            ->disk('public')
                                            ->directory('seo')
                                            ->maxSize(3072)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->deletable(true)
                                            ->columnSpanFull(),
                                    ])->columns(1),
                            ]),

                        // ── Tab: Marketing & Tracking ──────────────────────────────────
                        Forms\Components\Tabs\Tab::make('التسويق والتتبع')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Section::make('Google Analytics 4')
                                    ->description('يتتبع زوار صفحة مكتبك — احصل على الـ ID من Google Analytics → Admin → Data Streams.')
                                    ->schema([
                                        Forms\Components\TextInput::make('tracking.ga4_id')
                                            ->label('Measurement ID')
                                            ->placeholder('G-XXXXXXXXXX')
                                            ->helperText('يبدأ بـ G- — مثال: G-AB12CD34EF')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->maxLength(20),
                                    ]),

                                Forms\Components\Section::make('Google Tag Manager')
                                    ->description('يتيح لك إضافة أي سكريبت تتبع بدون تعديل الكود — احصل على الـ ID من tagmanager.google.com.')
                                    ->schema([
                                        Forms\Components\TextInput::make('tracking.gtm_id')
                                            ->label('Container ID')
                                            ->placeholder('GTM-XXXXXXX')
                                            ->helperText('يبدأ بـ GTM- — مثال: GTM-ABC1234')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->maxLength(15),
                                    ]),

                                Forms\Components\Section::make('Meta (Facebook) Pixel')
                                    ->description('لتتبع الإعلانات على فيسبوك وإنستغرام — احصل على الـ ID من Meta Business Suite → Events Manager.')
                                    ->schema([
                                        Forms\Components\TextInput::make('tracking.meta_pixel_id')
                                            ->label('Pixel ID')
                                            ->placeholder('1234567890123456')
                                            ->helperText('أرقام فقط — مثال: 1234567890123456')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->maxLength(20),
                                    ]),

                                Forms\Components\Section::make('TikTok Pixel')
                                    ->description('لتتبع الإعلانات على TikTok — احصل على الـ ID من TikTok Ads Manager → Assets → Events.')
                                    ->schema([
                                        Forms\Components\TextInput::make('tracking.tiktok_pixel_id')
                                            ->label('Pixel ID')
                                            ->placeholder('CXXXXXXXXXXXXXXX')
                                            ->helperText('مثال: C9ABCDEF12345678')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->maxLength(25),
                                    ]),

                                Forms\Components\Section::make('Snapchat Pixel')
                                    ->description('لتتبع الإعلانات على سناب شات — مهم جداً للسوق السعودي — احصل على الـ ID من Snap Ads Manager → Pixels.')
                                    ->schema([
                                        Forms\Components\TextInput::make('tracking.snapchat_pixel_id')
                                            ->label('Pixel ID')
                                            ->placeholder('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
                                            ->helperText('UUID بصيغة xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->maxLength(40),
                                    ]),

                                Forms\Components\Section::make('Google Search Console')
                                    ->description('لإثبات ملكية صفحتك لجوجل وظهورها في نتائج البحث — احصل على الكود من Search Console → Add Property → HTML Tag.')
                                    ->schema([
                                        Forms\Components\TextInput::make('tracking.search_console_token')
                                            ->label('Verification Token')
                                            ->placeholder('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                                            ->helperText('أدخل القيمة الموجودة داخل content="..." في الـ meta tag فقط — بدون الـ tag نفسه')
                                            ->extraInputAttributes(['dir' => 'ltr'])
                                            ->maxLength(100),
                                    ]),

                                Forms\Components\Placeholder::make('_tracking_priority_note')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        '<div class="rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm text-amber-800 dark:text-amber-300">'
                                        . '<p class="font-semibold mb-1">⚡ الأولوية</p>'
                                        . '<ul class="list-disc list-inside space-y-1 text-xs">'
                                        . '<li>إذا أدخلت GTM — سيُستخدم وحده (هو الأشمل ويغني عن الباقي إذا ضبطته من لوحته).</li>'
                                        . '<li>إذا أدخلت GA4 و Meta Pixel بدون GTM — سيُحقنان مباشرةً.</li>'
                                        . '<li>هذه الإعدادات خاصة بصفحة مكتبك فقط — مستقلة عن إعدادات منصة ميزان.</li>'
                                        . '</ul>'
                                        . '</div>'
                                    ))
                                    ->columnSpanFull(),
                            ]),

                        // ── Tab N: Custom Domain ───────────────────────────────────────
                        Forms\Components\Tabs\Tab::make('الدومين المخصص')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([

                                // ── Status banner when verified ────────────────────────
                                Forms\Components\Placeholder::make('_verified_banner')
                                    ->label('')
                                    ->content(fn () => $this->domainVerifiedAt
                                        ? new \Illuminate\Support\HtmlString(
                                            '<div class="rounded-xl border border-green-200 bg-green-50 dark:bg-green-950 dark:border-green-800 px-5 py-4 flex items-center gap-3">'
                                            . '<svg class="w-6 h-6 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                                            . '<div>'
                                            . '<p class="font-semibold text-green-800 dark:text-green-300">الدومين مُفعَّل بنجاح ✓</p>'
                                            . '<p class="text-sm text-green-700 dark:text-green-400 mt-0.5">زوارك يصلون لصفحة مكتبك مباشرةً على <span dir="ltr" class="font-mono">' . e($this->customDomain) . '</span> — منذ ' . e($this->domainVerifiedAt) . '</p>'
                                            . '</div>'
                                            . '</div>'
                                        )
                                        : new \Illuminate\Support\HtmlString('')
                                    )
                                    ->columnSpanFull(),

                                // ── Intro ──────────────────────────────────────────────
                                Forms\Components\Placeholder::make('_intro')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        '<div class="rounded-xl border border-blue-100 bg-blue-50 dark:bg-blue-950 dark:border-blue-800 px-5 py-4">'
                                        . '<p class="font-semibold text-blue-800 dark:text-blue-300 text-base">🌐 اربط دومينك الخاص بصفحة مكتبك</p>'
                                        . '<p class="text-sm text-blue-700 dark:text-blue-400 mt-1">بدلاً من أن يزور عملاؤك رابطاً طويلاً على ميزان، يمكنهم الوصول لصفحتك مباشرةً عبر دومينك — مثل <span dir="ltr" class="font-mono">www.amer-law.com</span> — بدون أي تغيير في الرابط.</p>'
                                        . '</div>'
                                    ))
                                    ->columnSpanFull(),

                                // ── Step 1 ─────────────────────────────────────────────
                                Forms\Components\Section::make('')
                                    ->schema([
                                        Forms\Components\Placeholder::make('_step1_header')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString(
                                                '<div class="flex items-center gap-3 mb-1">'
                                                . '<span class="flex items-center justify-center w-8 h-8 rounded-full bg-navy text-white text-sm font-bold shrink-0" style="background:#1E3A5F">١</span>'
                                                . '<p class="font-semibold text-gray-800 dark:text-gray-200 text-base">أدخل اسم الدومين الخاص بمكتبك</p>'
                                                . '</div>'
                                                . '<p class="text-sm text-gray-500 dark:text-gray-400 mr-11">اكتب الدومين الذي اشتريته من أي مزود (GoDaddy أو Namecheap أو أي شركة أخرى).</p>'
                                            ))
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('_custom_domain_display')
                                            ->label('الدومين المخصص')
                                            ->placeholder('www.amer-law.com')
                                            ->helperText('اكتب الدومين بدون https:// — مثال: www.amer-law.com أو amer-law.com')
                                            ->extraInputAttributes(['dir' => 'ltr', 'class' => 'font-mono'])
                                            ->default(fn () => $this->customDomain)
                                            ->live()
                                            ->dehydrated(false)
                                            ->afterStateUpdated(fn ($state) => $this->customDomain = $state ?? '')
                                            ->columnSpanFull(),

                                        Forms\Components\Placeholder::make('_step1_hint')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString(
                                                '<div class="rounded-lg bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm text-amber-800 dark:text-amber-300">'
                                                . '💾 بعد كتابة الدومين، اضغط <strong>حفظ الإعدادات</strong> في أسفل الصفحة — سيظهر لك رمز التحقق في الخطوة التالية.'
                                                . '</div>'
                                            ))
                                            ->columnSpanFull(),
                                    ]),

                                // ── Step 2 ─────────────────────────────────────────────
                                Forms\Components\Section::make('')
                                    ->schema([
                                        Forms\Components\Placeholder::make('_step2_header')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString(
                                                '<div class="flex items-center gap-3 mb-1">'
                                                . '<span class="flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-bold shrink-0" style="background:#1E3A5F">٢</span>'
                                                . '<p class="font-semibold text-gray-800 dark:text-gray-200 text-base">أضف هذين السجلَّين في إعدادات DNS الخاصة بدومينك</p>'
                                                . '</div>'
                                                . '<p class="text-sm text-gray-500 dark:text-gray-400 mr-11">ادخل على موقع مزود الدومين (GoDaddy أو Namecheap أو غيره)، ثم ابحث عن قسم <strong>DNS Management</strong> أو <strong>DNS Settings</strong>، وأضف السجلَّين التاليين:</p>'
                                            ))
                                            ->columnSpanFull(),

                                        Forms\Components\Placeholder::make('_dns_records')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString(
                                                '<div class="space-y-3">'

                                                // A Record
                                                . '<div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">'
                                                . '<div class="bg-blue-600 text-white text-xs font-bold px-4 py-2 flex items-center gap-2">'
                                                . '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                                                . 'السجل الأول — A Record (يوجّه الدومين لسيرفر ميزان)'
                                                . '</div>'
                                                . '<div class="bg-white dark:bg-gray-900 px-4 py-3 font-mono text-sm grid grid-cols-3 gap-4" dir="ltr">'
                                                . '<div><span class="block text-xs text-gray-400 mb-1 font-sans">Type</span><span class="font-bold text-blue-700">A</span></div>'
                                                . '<div><span class="block text-xs text-gray-400 mb-1 font-sans">Name / Host</span><span class="font-bold">@</span></div>'
                                                . '<div><span class="block text-xs text-gray-400 mb-1 font-sans">Value / Points to</span><span class="font-bold text-gray-800 dark:text-gray-200">' . (env('SERVER_IP') ?: '<span class="text-red-500">يُحدَّد من إعدادات السيرفر</span>') . '</span></div>'
                                                . '</div>'
                                                . '</div>'

                                                // TXT Record
                                                . '<div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">'
                                                . '<div class="bg-purple-600 text-white text-xs font-bold px-4 py-2 flex items-center gap-2">'
                                                . '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'
                                                . 'السجل الثاني — TXT Record (للتحقق من ملكية الدومين)'
                                                . '</div>'
                                                . '<div class="bg-white dark:bg-gray-900 px-4 py-3 font-mono text-sm grid grid-cols-3 gap-4" dir="ltr">'
                                                . '<div><span class="block text-xs text-gray-400 mb-1 font-sans">Type</span><span class="font-bold text-purple-700">TXT</span></div>'
                                                . '<div><span class="block text-xs text-gray-400 mb-1 font-sans">Name / Host</span><span class="font-bold">@</span></div>'
                                                . '<div><span class="block text-xs text-gray-400 mb-1 font-sans">Value</span><span class="font-bold text-gray-800 dark:text-gray-200">رمز التحقق — الخطوة الثالثة</span></div>'
                                                . '</div>'
                                                . '</div>'

                                                . '<p class="text-xs text-gray-400 flex items-center gap-1.5">⏱ قد يستغرق انتشار التغييرات في DNS من <strong>5 دقائق</strong> حتى <strong>48 ساعة</strong> — هذا طبيعي تماماً.</p>'
                                                . '</div>'
                                            ))
                                            ->columnSpanFull(),
                                    ]),

                                // ── Step 3 ─────────────────────────────────────────────
                                Forms\Components\Section::make('')
                                    ->schema([
                                        Forms\Components\Placeholder::make('_step3_header')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString(
                                                '<div class="flex items-center gap-3 mb-1">'
                                                . '<span class="flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-bold shrink-0" style="background:#1E3A5F">٣</span>'
                                                . '<p class="font-semibold text-gray-800 dark:text-gray-200 text-base">انسخ رمز التحقق وأضفه كـ TXT Record</p>'
                                                . '</div>'
                                                . '<p class="text-sm text-gray-500 dark:text-gray-400 mr-11">هذا الرمز يُثبت لنظام ميزان أن هذا الدومين فعلاً ملكك — انسخه وضعه في خانة <strong>Value</strong> الخاصة بـ TXT Record.</p>'
                                            ))
                                            ->columnSpanFull(),

                                        Forms\Components\Placeholder::make('_verify_token')
                                            ->label('رمز التحقق الخاص بمكتبك')
                                            ->content(fn () => $this->domainVerifyToken
                                                ? new \Illuminate\Support\HtmlString(
                                                    '<div class="space-y-2">'
                                                    . '<div class="relative">'
                                                    . '<code class="block font-mono text-sm bg-gray-100 dark:bg-gray-800 border-2 border-purple-200 dark:border-purple-700 rounded-lg px-4 py-3 select-all text-purple-800 dark:text-purple-300" dir="ltr">'
                                                    . e($this->domainVerifyToken)
                                                    . '</code>'
                                                    . '</div>'
                                                    . '<p class="text-xs text-gray-400">💡 انقر على الرمز لتحديده، ثم انسخه (Ctrl+C) والصقه في إعدادات DNS.</p>'
                                                    . '</div>'
                                                )
                                                : new \Illuminate\Support\HtmlString(
                                                    '<div class="rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-500">'
                                                    . '⬆️ أدخل الدومين في الخطوة الأولى واضغط <strong>حفظ الإعدادات</strong> أولاً — سيظهر الرمز هنا.'
                                                    . '</div>'
                                                )
                                            )
                                            ->columnSpanFull(),
                                    ]),

                                // ── Step 4 ─────────────────────────────────────────────
                                Forms\Components\Section::make('')
                                    ->schema([
                                        Forms\Components\Placeholder::make('_step4_header')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString(
                                                '<div class="flex items-center gap-3 mb-1">'
                                                . '<span class="flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-bold shrink-0" style="background:#1E3A5F">٤</span>'
                                                . '<p class="font-semibold text-gray-800 dark:text-gray-200 text-base">تحقق من الدومين</p>'
                                                . '</div>'
                                                . '<p class="text-sm text-gray-500 dark:text-gray-400 mr-11">بعد إضافة السجلَّين في DNS وانتظار الانتشار، اضغط الزر التالي — إذا كان كل شيء صحيحاً سيُفعَّل الدومين فوراً.</p>'
                                            ))
                                            ->columnSpanFull(),

                                        Forms\Components\Placeholder::make('_domain_status')
                                            ->label('الحالة الحالية')
                                            ->content(fn () => $this->domainVerifiedAt
                                                ? new \Illuminate\Support\HtmlString(
                                                    '<span class="inline-flex items-center gap-1.5 text-green-600 font-semibold">'
                                                    . '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                                                    . ' مُفعَّل — الدومين يعمل بنجاح'
                                                    . '</span>'
                                                )
                                                : new \Illuminate\Support\HtmlString(
                                                    '<span class="inline-flex items-center gap-1.5 text-amber-600">'
                                                    . '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                                    . ' في انتظار التحقق'
                                                    . '</span>'
                                                )
                                            ),

                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('verify_domain')
                                                ->label('تحقق من الدومين الآن')
                                                ->icon('heroicon-o-check-badge')
                                                ->color('success')
                                                ->size('lg')
                                                ->action('verifyDomain'),
                                        ])->columnSpanFull(),
                                    ]),

                            ]),

                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    /** Called by the "verify_domain" action button */
    public function verifyDomain(): void
    {
        $office = Office::query()->find(Auth::user()?->office_id ?? 0);
        if (! $office) return;

        try {
            $ok = app(DomainVerificationService::class)->verify($office);
        } catch (\RuntimeException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
            return;
        }

        if ($ok) {
            $office->update(['domain_verified_at' => now()]);
            $this->domainVerifiedAt = now()->format('Y-m-d H:i');
            Notification::make()
                ->title('تم التحقق بنجاح! ✓')
                ->body('دومينك مُفعَّل الآن ويمكن لزوارك الوصول إليه مباشرةً.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('لم يُعثر على رمز التحقق')
                ->body('تأكد من إضافة TXT Record الصحيح ثم انتظر انتشار DNS (قد يأخذ 5–60 دقيقة) وحاول مرة أخرى.')
                ->warning()
                ->send();
        }
    }

    public function save(): void
    {
        $office = Office::query()->find(Auth::user()?->office_id ?? 0);
        if (! $office) {
            return;
        }

        $data        = $this->form->getState();
        $oldSettings = $office->settings ?? [];
        $processor   = app(ImageProcessingService::class);

        // Process hero image if new one uploaded
        $newHero = $data['hero']['image_path'] ?? null;
        $oldHero = $oldSettings['hero']['image_path'] ?? null;
        if ($newHero && $newHero !== $oldHero) {
            try {
                $data['hero']['image_path'] = $processor->processHeroImage($newHero);
            } catch (\Throwable) {
                // keep original path if processing fails
            }
        }

        // Process logo if new one uploaded
        $newLogo = $data['branding']['logo_path'] ?? null;
        $oldLogo = $oldSettings['branding']['logo_path'] ?? null;
        if ($newLogo && $newLogo !== $oldLogo) {
            try {
                $data['branding']['logo_path'] = $processor->processLogoImage($newLogo);
            } catch (\Throwable) {
                // keep original path if processing fails
            }
        }

        // Process team photos if new ones uploaded
        if (! empty($data['team'])) {
            foreach ($data['team'] as $index => $member) {
                $newPhoto = $member['photo'] ?? null;
                $oldPhoto = $oldSettings['team'][$index]['photo'] ?? null;
                if ($newPhoto && $newPhoto !== $oldPhoto) {
                    try {
                        $data['team'][$index]['photo'] = $processor->processTeamPhoto($newPhoto);
                    } catch (\Throwable) {
                        // keep original path if processing fails
                    }
                }
            }
        }

        $office->update(['settings' => $data]);

        // Save the custom domain separately (it lives on the offices table, not in settings JSON)
        $newDomain = $this->customDomain
            ? Office::normalizeDomain($this->customDomain)
            : null;

        if ($newDomain !== ($office->custom_domain ?? null)) {
            try {
                $office->update(['custom_domain' => $newDomain ?: null]);
                // Booted hook regenerates token and clears domain_verified_at automatically
                $fresh = $office->fresh();
                $this->domainVerifyToken = $fresh->domain_verify_token;
                $this->domainVerifiedAt  = null;
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                Notification::make()
                    ->title('هذا الدومين مستخدم من مكتب آخر')
                    ->body('اختر دومين مختلف أو تواصل مع الدعم الفني.')
                    ->danger()
                    ->send();
                return;
            }
        }

        Notification::make()
            ->title('تم حفظ إعدادات الموقع بنجاح')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('حفظ الإعدادات')
                ->submit('save'),
        ];
    }

    /** Build extraAttributes for the image editor hint action button */
    protected function editorAttrs(string $fieldPath): array
    {
        $value = data_get($this->data, $fieldPath);
        $path  = is_array($value) ? (array_values($value)[0] ?? null) : $value;

        if (! $path) {
            return ['onclick' => 'alert(`ارفع صورة أولاً ثم احفظ ثم انقر تعديل`)'];
        }

        $url = asset('storage/' . $path);

        return [
            'data-img-url'  => $url,
            'data-img-path' => $path,
            'onclick'       => 'window.dispatchEvent(new CustomEvent(`open-image-editor`,{detail:{url:this.dataset.imgUrl,path:this.dataset.imgPath}}))',
        ];
    }
}
