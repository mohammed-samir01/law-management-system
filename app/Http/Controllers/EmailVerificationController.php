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
            return redirect()->intended('/admin')->with('success', 'تم تفعيل بريدك بنجاح.');
        }

        return back()->with('error', 'الرمز غير صحيح أو منتهي الصلاحية. حاول مجدداً.');
    }

    public function resend(EmailVerificationService $service)
    {
        $user = auth()->user();

        if ($user && ! $user->hasVerifiedEmail()) {
            $service->sendCode($user);
        }

        return back()->with('success', 'تم إرسال رمز جديد إلى بريدك.');
    }
}
