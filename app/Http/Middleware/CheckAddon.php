<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAddon
{
    public function handle(Request $request, Closure $next, string $slug): Response
    {
        $office = $request->user()?->office;

        if (! $office || ! $office->hasAddon($slug)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'هذه الميزة تتطلب تفعيل الإضافة أولاً.'], 403);
            }

            return redirect()
                ->route('filament.admin.pages.addons-marketplace-page')
                ->with('error', 'يلزم تفعيل الإضافة للوصول لهذه الميزة.');
        }

        return $next($request);
    }
}
