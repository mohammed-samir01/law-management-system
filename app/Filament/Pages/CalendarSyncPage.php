<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Str;

class CalendarSyncPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'مزامنة التقويم';
    protected static ?string $title           = 'مزامنة التقويم';
    protected static ?int    $navigationSort   = 10;
    protected static string  $view            = 'filament.pages.calendar-sync';

    public string $feedUrl = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->office?->hasAddon('calendar-sync') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->office?->hasAddon('calendar-sync') ?? false;
    }

    public function mount(): void
    {
        $office   = auth()->user()->office;
        $settings = $office->settings ?? [];

        if (empty($settings['calendar_token'])) {
            $settings['calendar_token'] = Str::random(40);
            $office->update(['settings' => $settings]);
        }

        $this->feedUrl = url('/calendar/' . $settings['calendar_token'] . '.ics');
    }
}
