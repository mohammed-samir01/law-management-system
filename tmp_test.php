<?php
use App\Models\PlatformSetting;
use App\Models\Plan;
use App\Models\Office;
use App\Services\AIUsageService;

PlatformSetting::put(['security' => ['rate' => ['login' => 4]]]);
echo "rate.login=" . PlatformSetting::get('security.rate.login', 5) . "\n";
echo "verify_enabled=" . var_export(PlatformSetting::get('security.email_verification_enabled', false), true) . "\n";
foreach (['trial','basic','pro','enterprise'] as $slug) {
    $p = Plan::where('slug',$slug)->first();
    echo "$slug ai=" . ($p->ai_enabled?'1':'0') . " max=" . var_export($p->max_ai_requests_monthly, true) . "\n";
}
$office = Office::first();
$svc = app(AIUsageService::class);
echo "office_ai=" . ($svc->aiEnabled($office)?'yes':'no') . " used=" . $svc->requestsUsed($office) . " limit=" . var_export($svc->requestsLimit($office), true) . " exceeded=" . ($svc->exceeded($office)?'1':'0') . "\n";
$u = App\Models\User::first();
echo "mustverify=" . ($u instanceof Illuminate\Contracts\Auth\MustVerifyEmail ? '1':'0') . " verified=" . ($u->hasVerifiedEmail()?'1':'0') . "\n";
PlatformSetting::put(['security' => ['rate' => ['login' => 5]]]);
echo "DONE\n";
