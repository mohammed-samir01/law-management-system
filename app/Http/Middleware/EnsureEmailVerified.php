<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Enforced only when the platform owner enabled it (after configuring SMTP).
        if (! PlatformSetting::get('security.email_verification_enabled', false)) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || $user->hasVerifiedEmail()) {
            return $next($request);
        }

        // Always allow the verification + logout routes through.
        if ($request->routeIs('verification.*')
            || $request->is('verify-email*')
            || $request->routeIs('*.logout')
            || $request->is('*/logout')) {
            return $next($request);
        }

        return redirect()->route('verification.notice');
    }
}
