<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Not logged in, or a user not tied to an office — let other guards handle it.
        if (! $user || ! $user->office_id) {
            return $next($request);
        }

        // Platform owner is never blocked.
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Always allow billing + expired + email-verification pages so the
        // office can renew and re-verify.
        if ($request->routeIs('billing.*', 'subscription.expired', 'verification.*')
            || $request->is('admin/billing*', 'subscription/expired', 'verify-email*')) {
            return $next($request);
        }

        $office = $user->office;

        if (! $office || ! $office->hasUsableSubscription()) {
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
