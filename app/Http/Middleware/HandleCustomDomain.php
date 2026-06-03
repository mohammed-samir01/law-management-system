<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LandingController;
use App\Models\Office;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class HandleCustomDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host    = strtolower($request->getHost());
        $appHost = strtolower(parse_url(config('app.url'), PHP_URL_HOST) ?? '');

        // Always pass through requests on the main Mizan domain or its subdomains
        if ($host === $appHost || ($appHost && str_ends_with($host, '.' . $appHost))) {
            return $next($request);
        }

        // Only intercept the root landing path and the contact form endpoint
        $path = rtrim($request->getPathInfo(), '/');
        if (! in_array($path, ['', '/contact'], true)) {
            return $next($request);
        }

        // Look up a verified office for this host — cached for 5 minutes
        $office = Cache::remember(
            "custom_domain:{$host}",
            300,
            fn () => Office::withoutGlobalScopes()->byVerifiedDomain($host)->first()
        );

        if (! $office) {
            return $next($request);
        }

        // POST /contact — inject the office slug so the existing contact() handler
        // routes the ticket to the correct office, then continue normal routing
        if ($request->isMethod('POST') && $path === '/contact') {
            $request->merge(['office_slug' => $office->slug]);
            return $next($request);
        }

        // Render the office landing page inline (visitor stays on their custom domain)
        return app(LandingController::class)->renderForOffice($office);
    }
}
