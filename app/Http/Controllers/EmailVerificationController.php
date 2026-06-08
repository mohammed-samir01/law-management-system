<?php

namespace App\Http\Controllers;

use App\Services\EmailVerificationService;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        $user = auth()->user();

        if ($user && $user->hasVerifiedEmail()) {
            return redirect('/admin');
        }

        return view('auth.verify-otp');
    }

    public function verify(Request $request, EmailVerificationService $service)
    {
        $request->validate(['code' => ['required', 'string', 'max:12']]);

        $user = auth()->user();

        if ($service->verify($user, $request->code)) {
            if (session()->pull('needs_profile_setup')) {
                return redirect()->route('register.profile')->with('success', 'تم تفعيل بريدك بنجاح.');
            }

            return redirect()->intended('/admin')->with('success', 'تم تفعيل بريدك بنجاح.');
        }

        return back()->with('error', 'الرمز غير صحيح أو منتهي الصلاحية. حاول مجدداً.');
    }

    public function resend(EmailVerificationService $service)
    {
        $user = auth()->user();

        if (! $user || $user->hasVerifiedEmail()) {
            return redirect('/admin');
        }

        try {
            $service->sendCode($user);
            return back()->with('success', 'تم إرسال رمز جديد. تحقق من Inbox والـ Spam/Junk.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OTP resend failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'فشل إرسال الرمز — تحقق من إعدادات البريد في لوحة التحكم أو تواصل مع الدعم.');
        }
    }
}
