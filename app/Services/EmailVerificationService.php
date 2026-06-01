<?php

namespace App\Services;

use App\Models\EmailOtp;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Notifications\VerifyEmailOtpNotification;
use Illuminate\Support\Facades\Hash;

class EmailVerificationService
{
    /**
     * Generate a fresh OTP for the user, store it hashed, and email it.
     */
    public function sendCode(User $user): void
    {
        $length = (int) PlatformSetting::get('otp.length', 6);
        $ttl    = (int) PlatformSetting::get('otp.ttl_minutes', 15);

        $min  = (int) str_pad('1', $length, '0');          // e.g. 100000 for length 6
        $max  = (int) str_pad('', $length, '9');           // e.g. 999999
        $code = (string) random_int($min, $max);

        // One active OTP per user.
        EmailOtp::where('user_id', $user->id)->delete();

        EmailOtp::create([
            'user_id'    => $user->id,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes($ttl),
            'attempts'   => 0,
        ]);

        $user->notify(new VerifyEmailOtpNotification($code, $ttl));
    }

    /**
     * Verify a submitted code. Returns true on success (marks email verified).
     */
    public function verify(User $user, string $code): bool
    {
        $otp = EmailOtp::where('user_id', $user->id)->latest()->first();

        if (! $otp || $otp->isExpired()) {
            return false;
        }

        $maxAttempts = (int) PlatformSetting::get('otp.max_attempts', 5);
        if ($otp->attempts >= $maxAttempts) {
            return false;
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            return false;
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $otp->delete();

        return true;
    }
}
