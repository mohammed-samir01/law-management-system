<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use App\Services\Billing\PlatformBillingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PlatformBillingSettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'بوابة دفع المنصة';
    protected static ?string $title           = 'إعدادات بوابة دفع المنصة';
    protected static ?int    $navigationSort   = 1;
    protected static string  $view            = 'filament.pages.platform-billing-settings';

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
            'billing_gateway'   => $row->billing_gateway ?? 'paymob',
            'billing_test_mode' => $row->billing_test_mode ?? true,
            'config'            => $row->billing_config ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
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
                        Forms\Components\TextInput::make('config.api_key')->label('API Key')->password()->revealable()->autocomplete(false),
                        Forms\Components\TextInput::make('config.integration_id')->label('Integration ID'),
                        Forms\Components\TextInput::make('config.iframe_id')->label('Iframe ID'),
                        Forms\Components\TextInput::make('config.hmac_secret')->label('HMAC Secret')->password()->revealable()->autocomplete(false),
                    ])->columns(2),

                Forms\Components\Section::make('مفاتيح Stripe')
                    ->visible(fn (Forms\Get $get) => $get('billing_gateway') === 'stripe')
                    ->schema([
                        Forms\Components\TextInput::make('config.secret_key')->label('Secret Key')->password()->revealable()->autocomplete(false),
                        Forms\Components\TextInput::make('config.webhook_secret')->label('Webhook Secret')->password()->revealable()->autocomplete(false),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        PlatformSetting::singleton()->update([
            'billing_gateway'   => $state['billing_gateway'] ?? 'paymob',
            'billing_test_mode' => $state['billing_test_mode'] ?? true,
            'billing_config'    => array_filter($state['config'] ?? [], fn ($v) => $v !== null && $v !== ''),
        ]);

        Notification::make()
            ->title('تم حفظ إعدادات بوابة الدفع بنجاح')
            ->success()
            ->send();
    }

    public function testConnection(): void
    {
        // Persist first so the service reads the latest keys.
        $this->save();

        try {
            $ok = PlatformBillingService::gateway()->testConnection();

            Notification::make()
                ->title($ok ? 'نجح الاتصال بالبوابة ✓' : 'فشل الاتصال — تحقق من المفاتيح')
                ->{$ok ? 'success' : 'danger'}()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('تعذّر الاتصال')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('حفظ الإعدادات')
                ->submit('save'),

            \Filament\Actions\Action::make('test')
                ->label('اختبار الاتصال')
                ->color('gray')
                ->action('testConnection'),
        ];
    }
}
