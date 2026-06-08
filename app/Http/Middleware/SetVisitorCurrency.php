<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetVisitorCurrency
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('visitor_currency')) {
            // Cloudflare sets this header automatically
            $country  = strtoupper($request->header('CF-IPCountry', ''));
            $map      = config('currencies.country_map', []);
            $fallback = config('currencies.fallback', 'USD');

            $currency = $map[$country] ?? $fallback;

            session(['visitor_currency' => $currency, 'visitor_country' => $country]);
        }

        return $next($request);
    }
}
