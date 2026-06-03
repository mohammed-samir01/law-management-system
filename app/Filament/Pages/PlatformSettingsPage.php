<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PlatformSettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'صفحة منصة ميزان';
    protected static ?string $title           = 'تخصيص صفحة منصة ميزان';
    protected static ?int    $navigationSort  = 0;
    protected static string  $view            = 'filament.pages.platform-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill(PlatformSetting::current());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('platform')->tabs([

                    Forms\Components\Tabs\Tab::make('الهوية')->schema([
                        Forms\Components\TextInput::make('brand.name_ar')->label('اسم المنصة (عربي)')->required(),
                        Forms\Components\TextInput::make('brand.name_en')->label('اسم المنصة (إنجليزي)'),
                        Forms\Components\FileUpload::make('brand.logo_path')
                            ->label('شعار ميزان')
                            ->image()
                            ->directory('platform')
                            ->imageEditor()
                            ->maxSize(2048),
                        Forms\Components\FileUpload::make('brand.favicon_path')
                            ->label('Favicon (أيقونة التبويب)')
                            ->helperText('PNG أو ICO أو SVG — يُفضَّل 512×512 px — يظهر في تبويب المتصفح')
                            ->image()
                            ->directory('platform')
                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/svg+xml', 'image/webp'])
                            ->maxSize(512),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('القسم الرئيسي')->schema([
                        Forms\Components\TextInput::make('hero.heading_ar')->label('العنوان الرئيسي (عربي)')->required()->columnSpanFull(),
                        Forms\Components\TextInput::make('hero.heading_en')->label('العنوان الرئيسي (إنجليزي)')->columnSpanFull(),
                        Forms\Components\Textarea::make('hero.subtitle_ar')->label('الوصف (عربي)')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('hero.subtitle_en')->label('الوصف (إنجليزي)')->rows(3)->columnSpanFull(),
                        Forms\Components\TextInput::make('hero.cta_ar')->label('زر الدعوة (عربي)'),
                        Forms\Components\TextInput::make('hero.cta_en')->label('زر الدعوة (إنجليزي)'),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('الإحصائيات')->schema([
                        Forms\Components\Repeater::make('stats')
                            ->label('أرقام المنصة')
                            ->schema([
                                Forms\Components\TextInput::make('value')->label('القيمة')->required(),
                                Forms\Components\TextInput::make('suffix')->label('اللاحقة (+ / %)'),
                                Forms\Components\TextInput::make('label_ar')->label('الوصف (عربي)')->required(),
                                Forms\Components\TextInput::make('label_en')->label('الوصف (إنجليزي)'),
                            ])
                            ->columns(4)
                            ->addActionLabel('إضافة رقم')
                            ->defaultItems(0),
                    ]),

                    Forms\Components\Tabs\Tab::make('المميزات')->schema([
                        Forms\Components\Repeater::make('features')
                            ->label('بطاقات المميزات')
                            ->schema([
                                Forms\Components\Select::make('icon')->label('الأيقونة')->options([
                                    'scale' => 'القضايا', 'calendar' => 'الجلسات', 'users' => 'العملاء',
                                    'document' => 'الوثائق', 'cash' => 'المالية', 'sparkles' => 'الذكاء الاصطناعي',
                                ])->default('document'),
                                Forms\Components\TextInput::make('title_ar')->label('العنوان (عربي)')->required(),
                                Forms\Components\TextInput::make('title_en')->label('العنوان (إنجليزي)'),
                                Forms\Components\Textarea::make('desc_ar')->label('الوصف (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('desc_en')->label('الوصف (إنجليزي)')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->addActionLabel('إضافة ميزة')
                            ->defaultItems(0),
                    ]),

                    Forms\Components\Tabs\Tab::make('لماذا نحن')->schema([
                        Forms\Components\Repeater::make('why_us')
                            ->label('أسباب الاختيار')
                            ->schema([
                                Forms\Components\Select::make('icon')->label('الأيقونة')->options([
                                    'shield' => 'درع (أمان)', 'bolt' => 'برق (سرعة)', 'headset' => 'دعم',
                                    'refresh' => 'تحديث', 'star' => 'نجمة', 'heart' => 'قلب',
                                ])->default('shield'),
                                Forms\Components\TextInput::make('title_ar')->label('العنوان (عربي)')->required(),
                                Forms\Components\TextInput::make('title_en')->label('العنوان (إنجليزي)'),
                                Forms\Components\Textarea::make('desc_ar')->label('الوصف (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('desc_en')->label('الوصف (إنجليزي)')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->addActionLabel('إضافة سبب')
                            ->defaultItems(0),
                    ]),

                    Forms\Components\Tabs\Tab::make('التواصل')->schema([
                        Forms\Components\TextInput::make('contact.phone')->label('الهاتف'),
                        Forms\Components\TextInput::make('contact.email')->label('البريد الإلكتروني')->email(),
                        Forms\Components\TextInput::make('contact.whatsapp')->label('واتساب (أرقام فقط)'),
                        Forms\Components\TextInput::make('contact.address_ar')->label('العنوان (عربي)'),
                        Forms\Components\TextInput::make('contact.address_en')->label('العنوان (إنجليزي)'),
                        Forms\Components\TextInput::make('contact.facebook')->label('فيسبوك')->url(),
                        Forms\Components\TextInput::make('contact.twitter_x')->label('X (تويتر)')->url(),
                        Forms\Components\TextInput::make('contact.instagram')->label('إنستغرام')->url(),
                        Forms\Components\TextInput::make('contact.linkedin')->label('لينكدإن')->url(),
                    ])->columns(2),

                    // ── Tab: Marketing & Tracking ──────────────────────────────
                    Forms\Components\Tabs\Tab::make('التسويق والتتبع')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            Forms\Components\Section::make('Google Analytics 4')
                                ->description('يتتبع زوار صفحة ميزان الرئيسية — احصل على الـ ID من Google Analytics → Admin → Data Streams.')
                                ->schema([
                                    Forms\Components\TextInput::make('tracking.ga4_id')
                                        ->label('Measurement ID')
                                        ->placeholder('G-XXXXXXXXXX')
                                        ->helperText('يبدأ بـ G- — مثال: G-AB12CD34EF')
                                        ->extraInputAttributes(['dir' => 'ltr'])
                                        ->maxLength(20),
                                ])->columns(1),

                            Forms\Components\Section::make('Google Tag Manager')
                                ->description('يتيح لك إضافة أي سكريبت تتبع (Meta Pixel، إلخ) بدون تعديل الكود — احصل على الـ ID من tagmanager.google.com.')
                                ->schema([
                                    Forms\Components\TextInput::make('tracking.gtm_id')
                                        ->label('Container ID')
                                        ->placeholder('GTM-XXXXXXX')
                                        ->helperText('يبدأ بـ GTM- — مثال: GTM-ABC1234')
                                        ->extraInputAttributes(['dir' => 'ltr'])
                                        ->maxLength(15),
                                ])->columns(1),

                            Forms\Components\Section::make('Google Search Console')
                                ->description('لإثبات ملكية منصة ميزان لجوجل — احصل على الكود من Search Console → Add Property → HTML Tag.')
                                ->schema([
                                    Forms\Components\TextInput::make('tracking.search_console_token')
                                        ->label('Verification Token')
                                        ->placeholder('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                                        ->helperText('أدخل قيمة الـ content="..." فقط بدون الـ tag')
                                        ->extraInputAttributes(['dir' => 'ltr'])
                                        ->maxLength(100),
                                ])->columns(1),

                            Forms\Components\Placeholder::make('_tracking_note')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString(
                                    '<div class="rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 px-4 py-3 text-sm text-blue-800 dark:text-blue-300">'
                                    . '<p class="font-semibold mb-1">💡 ملاحظة</p>'
                                    . '<ul class="list-disc list-inside space-y-1 text-xs">'
                                    . '<li>إذا أدخلت كلاهما — سيُستخدم GTM فقط (هو الأشمل).</li>'
                                    . '<li>إذا أدخلت GA4 فقط — سيُحقن مباشرةً في الصفحة.</li>'
                                    . '<li>هذه الإعدادات تخص صفحات منصة ميزان فقط — إعدادات كل مكتب منفصلة في لوحة المكتب.</li>'
                                    . '</ul>'
                                    . '</div>'
                                ))
                                ->columnSpanFull(),
                        ]),

                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        PlatformSetting::singleton()->update(['data' => $data]);

        Notification::make()
            ->title('تم حفظ إعدادات منصة ميزان بنجاح')
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
}
