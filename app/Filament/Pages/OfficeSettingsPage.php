<?php

namespace App\Filament\Pages;

use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class OfficeSettingsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'إعدادات المكتب';
    protected static ?string $title           = 'إعدادات المكتب';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.office-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $office = Office::find(Auth::user()->office_id);
        $this->form->fill($office?->toArray() ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('settings.office_info'))
                    ->schema([
                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('offices.name') . ' (عربي)')
                            ->required(),

                        Forms\Components\TextInput::make('name.en')
                            ->label(__('offices.name') . ' (English)'),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('offices.phone'))
                            ->tel(),

                        Forms\Components\TextInput::make('email')
                            ->label(__('offices.email'))
                            ->email(),

                        Forms\Components\TextInput::make('tax_number')
                            ->label(__('offices.tax_number')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('offices.is_active'))
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make(__('settings.office_address'))
                    ->schema([
                        Forms\Components\TextInput::make('address.street')
                            ->label(__('offices.street')),

                        Forms\Components\TextInput::make('address.city')
                            ->label(__('offices.city')),

                        Forms\Components\TextInput::make('address.country')
                            ->label(__('offices.country')),
                    ])->columns(3)->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data   = $this->form->getState();
        $office = Office::find(Auth::user()->office_id);

        if ($office) {
            $office->update($data);
            Notification::make()
                ->title(__('settings.saved'))
                ->success()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label(__('settings.save'))
                ->submit('save'),
        ];
    }
}
