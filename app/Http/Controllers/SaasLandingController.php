<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\Office;
use App\Models\Plan;
use App\Models\PlatformSetting;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;

class SaasLandingController extends Controller
{
    public function index()
    {
        $platform    = PlatformSetting::current();
        $plans       = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $liveStats   = $this->getLiveStats();
        $homepageVis = $platform['homepage'] ?? [];

        return view('saas.landing', compact('platform', 'plans', 'liveStats', 'homepageVis'));
    }

    public function pricing()
    {
        $platform = PlatformSetting::current();
        $plans    = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('saas.pricing', compact('platform', 'plans'));
    }

    public function sitemap()
    {
        $offices = Cache::remember('sitemap_offices', 600, fn () =>
            Office::withoutGlobalScopes()
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->select('slug', 'updated_at')
                ->get()
        );

        $content = view('seo.sitemap-platform', compact('offices'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    public function sitemapPages()
    {
        $content = view('seo.sitemap-pages')->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    private function getLiveStats(): array
    {
        return Cache::remember('saas_live_stats', 300, function () {
            return [
                'offices'       => Office::withoutGlobalScopes()->whereNull('deleted_at')->where('is_active', true)->count(),
                'cases'         => LegalCase::withoutGlobalScopes()->count(),
                'trial_offices' => Subscription::withoutGlobalScopes()->where('status', 'trial')->count(),
            ];
        });
    }
}
