<?php

namespace App\Filament\Pages;

use App\Models\Office;
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

    public function mount(): void
    {
        $office   = Office::query()->find(Auth::user()?->office_id ?? 0);
        $settings = array_replace_recursive(
            app(\App\Http\Controllers\LandingController::class)->getDefaultSettings(),
            $office?->settings ?? []
        );
        $this->form->fill($settings);
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
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
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
