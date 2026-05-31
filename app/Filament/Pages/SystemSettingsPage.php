<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SystemSettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات النظام';
    protected static ?string $title           = 'إعدادات النظام';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.system-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Platform-wide settings (app name, locale, timezone, cache) — owner only.
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'app_name'       => config('app.name'),
            'default_locale' => config('app.locale', 'ar'),
            'timezone'       => config('app.timezone', 'Asia/Riyadh'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('settings.system_settings'))
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label(__('settings.app_name'))
                            ->required(),

                        Forms\Components\Select::make('default_locale')
                            ->label(__('settings.default_locale'))
                            ->options([
                                'ar' => 'العربية',
                                'en' => 'English',
                            ])
                            ->required(),

                        Forms\Components\Select::make('timezone')
                            ->label(__('settings.timezone'))
                            ->options([
                                'Asia/Riyadh' => 'الرياض (AST)',
                                'Africa/Cairo' => 'القاهرة (EET)',
                                'UTC'          => 'UTC',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make(__('settings.cache_management'))
                    ->schema([
                        Forms\Components\Placeholder::make('cache_info')
                            ->label(__('settings.cache_info'))
                            ->content(__('settings.cache_info_body')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();

        Notification::make()
            ->title(__('settings.saved'))
            ->body(__('settings.restart_required'))
            ->warning()
            ->send();
    }

    public function clearCache(): void
    {
        Artisan::call('optimize:clear');

        Notification::make()
            ->title(__('settings.cache_cleared'))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label(__('settings.save'))
                ->submit('save'),

            \Filament\Actions\Action::make('clear_cache')
                ->label(__('settings.clear_cache'))
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->action('clearCache'),
        ];
    }
}
