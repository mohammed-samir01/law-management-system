<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\SetAdminLocale;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors(['primary' => Color::hex('#1E3A5F')])
            ->font('Tajawal', 'https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->globalSearch(true)
            ->navigationGroups([
                'القضايا',
                'العملاء',
                'المالية',
                'الوثائق',
                'الإدارة',
                'الإعدادات',
            ])
            ->middleware([
                SetAdminLocale::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\CheckSubscription::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->renderHook('panels::head.end', fn (): HtmlString => new HtmlString(
                '<link rel="stylesheet" href="' . asset('css/cropper.min.css') . '">'
            ))
            ->renderHook('panels::body.end', fn (): HtmlString => new HtmlString(
                '<script src="' . asset('js/cropper.min.js') . '"></script>' .
                '<script src="' . asset('js/image-editor.js') . '"></script>'
            ));
    }
}
