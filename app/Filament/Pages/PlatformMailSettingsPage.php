<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class PlatformMailSettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات البريد';
    protected static ?string $title           = 'إعدادات البريد والتحقق';
    protected static ?int    $navigationSort   = 2;
    protected static string  $view            = 'filament.pages.platform-mail-settings';

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
        $row = PlatformSetting::singleton();

        $this->form->fill([
            'mail'                        => $row->mail_config ?? [],
            'otp_length'                  => PlatformSetting::get('otp.length', 6),
            'otp_ttl_minutes'             => PlatformSetting::get('otp.ttl_minutes', 15),
            'otp_max_attempts'            => PlatformSetting::get('otp.max_attempts', 5),
            'email_verification_enabled'  => PlatformSetting::get('security.email_verification_enabled', false),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
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
            ])
            ->statePath('data');
    }

    public function save(): void
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

    public function sendTest(): void
    {
        $this->save();

        $mail = PlatformSetting::mail();
        if (empty($mail['host'])) {
            Notification::make()->title('اضبط خادم البريد أولاً')->danger()->send();
            return;
        }

        try {
            // Apply current settings at runtime then send.
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

            Mail::raw('رسالة اختبار من منصة ميزان — إعدادات البريد تعمل بنجاح ✓', function ($m) {
                $m->to(auth()->user()->email)->subject('اختبار بريد ميزان');
            });

            Notification::make()->title('تم إرسال إيميل اختبار إلى ' . auth()->user()->email)->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('فشل الإرسال')->body($e->getMessage())->danger()->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')->label('حفظ الإعدادات')->submit('save'),
            \Filament\Actions\Action::make('sendTest')->label('إرسال إيميل اختبار')->color('gray')->action('sendTest'),
        ];
    }
}
