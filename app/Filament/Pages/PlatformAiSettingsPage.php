<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PlatformAiSettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات الذكاء الاصطناعي';
    protected static ?string $title           = 'إعدادات الذكاء الاصطناعي';
    protected static ?int    $navigationSort   = 4;
    protected static string  $view            = 'filament.pages.platform-ai-settings';

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
            'model'       => PlatformSetting::get('ai.model', config('services.openai.model', 'gpt-4o')),
            'max_tokens'  => PlatformSetting::get('ai.max_tokens', 2000),
            'temperature' => PlatformSetting::get('ai.temperature', 0.3),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('نموذج الذكاء الاصطناعي')
                    ->description('يُطبَّق على كل طلبات الذكاء الاصطناعي. حدود الاستهلاك لكل خطة تُضبط من صفحة «خطط الاشتراك».')
                    ->schema([
                        Forms\Components\Select::make('model')->label('النموذج')->options([
                            'gpt-4o'      => 'GPT-4o',
                            'gpt-4o-mini' => 'GPT-4o mini (أرخص)',
                            'gpt-4-turbo' => 'GPT-4 Turbo',
                        ])->required(),
                        Forms\Components\TextInput::make('max_tokens')->label('أقصى توكنز للطلب')->numeric()->minValue(256)->maxValue(8000)->required(),
                        Forms\Components\TextInput::make('temperature')->label('درجة الحرارة (0–1)')->numeric()->step(0.1)->minValue(0)->maxValue(1)->required(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $s = $this->form->getState();

        PlatformSetting::put([
            'ai' => [
                'model'       => $s['model'],
                'max_tokens'  => (int) $s['max_tokens'],
                'temperature' => (float) $s['temperature'],
            ],
        ]);

        Notification::make()->title('تم حفظ إعدادات الذكاء الاصطناعي')->success()->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')->label('حفظ الإعدادات')->submit('save'),
        ];
    }
}
