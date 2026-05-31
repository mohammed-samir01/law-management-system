<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

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
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
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

                    Forms\Components\Tabs\Tab::make('التواصل')->schema([
                        Forms\Components\TextInput::make('contact.phone')->label('الهاتف'),
                        Forms\Components\TextInput::make('contact.email')->label('البريد الإلكتروني')->email(),
                        Forms\Components\TextInput::make('contact.whatsapp')->label('واتساب'),
                        Forms\Components\TextInput::make('contact.address')->label('العنوان'),
                    ])->columns(2),

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
