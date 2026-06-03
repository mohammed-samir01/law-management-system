<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPortalLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->user()?->language;

        if (in_array($lang, ['ar', 'en'], true)) {
            app()->setLocale($lang);
        }

        return $next($request);
    }
}
