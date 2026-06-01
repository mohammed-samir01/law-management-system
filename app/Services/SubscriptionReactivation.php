<?php

namespace App\Services;

use App\Models\PlatformSetting;
use App\Models\User;

class SubscriptionReactivation
{
    /**
     * When an office is reactivated after suspension, force its users to
     * re-verify their email (only if email verification is enabled).
     * Office admins receive a fresh OTP immediately.
     */
    public static function forceReVerify(int $officeId): void
    {
        if (! PlatformSetting::get('security.email_verification_enabled', false)) {
            return;
        }

        $users = User::where('office_id', $officeId)->get();

        foreach ($users as $user) {
            $user->forceFill(['email_verified_at' => null])->save();
        }

        // Send OTP to office admins so they can regain access.
        $service = app(EmailVerificationService::class);
        foreach ($users as $user) {
            if ($user->hasRole('office_admin')) {
                $service->sendCode($user);
            }
        }
    }
}
