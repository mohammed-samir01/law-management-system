<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlatformSetting;

class SaasLandingController extends Controller
{
    public function index()
    {
        $platform = PlatformSetting::current();
        $plans    = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('saas.landing', compact('platform', 'plans'));
    }

    public function pricing()
    {
        $platform = PlatformSetting::current();
        $plans    = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('saas.pricing', compact('platform', 'plans'));
    }
}
