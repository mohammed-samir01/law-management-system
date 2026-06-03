<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/mobile.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/desktop.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Must run before routing so custom domains are intercepted first
        $middleware->prepend(\App\Http\Middleware\HandleCustomDomain::class);

        // Redirect unauthenticated users: portal/* → portal login, else admin login
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('mobile/*') || $request->is('mobile')) {
                return route('mobile.login');
            }
            if ($request->is('portal/*') || $request->is('portal')) {
                return route('portal.login');
            }
            if ($request->is('desktop/*') || $request->is('desktop')) {
                return route('desktop.login');
            }
            return '/admin/login';
        });

        // Redirect already-authenticated users away from guest-only pages
        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('mobile/*') || $request->is('mobile')) {
                if (auth()->check() && auth()->user()->hasRole('client')) {
                    return route('mobile.client.dashboard');
                }
                return route('mobile.lawyer.dashboard');
            }
            if ($request->is('portal/*') || $request->is('portal')) {
                if (auth()->check() && auth()->user()->hasRole('client')) {
                    return route('portal.dashboard');
                }
                return '/admin';
            }
            return '/admin';
        });

        $middleware->alias([
            'client.portal'      => \App\Http\Middleware\EnsureClientRole::class,
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'verified.otp'       => \App\Http\Middleware\EnsureEmailVerified::class,
            'addon'              => \App\Http\Middleware\CheckAddon::class,
            'portal.locale'      => \App\Http\Middleware\SetPortalLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
