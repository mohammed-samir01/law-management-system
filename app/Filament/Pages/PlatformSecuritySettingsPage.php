<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PlatformSecuritySettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'الأمان والوسائط';
    protected static ?string $title           = 'إعدادات الأمان والوسائط';
    protected static ?int    $navigationSort   = 3;
    protected static string  $view            = 'filament.pages.platform-security-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'rate_login'    => PlatformSetting::get('security.rate.login', 5),
            'rate_register' => PlatformSetting::get('security.rate.register', 3),
            'rate_contact'  => PlatformSetting::get('security.rate.contact', 5),
            'rate_otp'      => PlatformSetting::get('security.rate.otp', 3),
            'rate_uploads'  => PlatformSetting::get('security.rate.uploads', 30),
            'rate_ai'       => PlatformSetting::get('security.rate.ai', 20),
            'rate_api'      => PlatformSetting::get('security.rate.api', 120),
            'max_upload_kb' => PlatformSetting::get('media.max_upload_kb', 10240),
            'avatar_max_kb' => PlatformSetting::get('media.avatar_max_kb', 2048),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
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
                        Forms\Components\TextInput::make('max_upload_kb')->label('أقصى حجم ملف (كيلوبايت)')->numeric()->minValue(256)->required()
                            ->helperText('10240 = 10 ميجابايت'),
                        Forms\Components\TextInput::make('avatar_max_kb')->label('أقصى حجم صورة شخصية (كيلوبايت)')->numeric()->minValue(128)->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
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

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')->label('حفظ الإعدادات')->submit('save'),
        ];
    }
}
