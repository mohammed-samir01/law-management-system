<?php

namespace App\Filament\Pages;

use App\Services\BackupService;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CloudBackupPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-cloud-arrow-up';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'النسخ الاحتياطي';
    protected static ?string $title           = 'النسخ الاحتياطي السحابي';
    protected static ?int    $navigationSort   = 11;
    protected static string  $view            = 'filament.pages.cloud-backup';

    public static function canAccess(): bool
    {
        $u = auth()->user();
        return ($u?->office?->hasAddon('cloud-backup') ?? false) && $u->hasRole('office_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function download(): StreamedResponse
    {
        $officeId = auth()->user()->office_id;
        $data     = app(BackupService::class)->exportOffice($officeId);

        $filename = 'mizan-backup-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(
            fn () => print(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)),
            $filename,
            ['Content-Type' => 'application/json'],
        );
    }
}
