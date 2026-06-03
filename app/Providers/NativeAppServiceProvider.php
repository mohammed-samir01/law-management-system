<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\Menu;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        Window::open()
            ->width(1280)
            ->height(820)
            ->minWidth(900)
            ->minHeight(600)
            ->title('ميزان — إدارة مكاتب المحاماة')
            ->route('desktop.dashboard');

        MenuBar::create()
            ->tooltip('ميزان')
            ->contextMenu(
                Menu::make(
                    Menu::link(route('desktop.dashboard'), 'لوحة التحكم'),
                    Menu::link(route('desktop.cases'),     'القضايا'),
                    Menu::link(route('desktop.calendar'),  'الجلسات'),
                    Menu::separator(),
                    Menu::label(auth()->user()?->name ?? 'ميزان'),
                )
            );
    }

    public function phpIni(): array
    {
        return [];
    }
}
