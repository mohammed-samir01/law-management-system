<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('mobile.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withInput()->with('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة');
        }

        if (! $user->is_active) {
            return back()->withInput()->with('error', 'هذا الحساب غير مفعّل');
        }

        // 2FA check
        if ($user->two_factor_confirmed_at) {
            if (! $request->filled('two_factor_code')) {
                return back()->withInput()->with('requires_2fa', true);
            }
            $google2fa = new Google2FA();
            if (! $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->two_factor_code)) {
                return back()->withInput()->with('error', 'رمز التحقق الثنائي غير صحيح');
            }
        }

        Auth::login($user, true);

        // Route by role
        if ($user->hasRole('client')) {
            return redirect()->route('mobile.client.dashboard');
        }

        return redirect()->route('mobile.lawyer.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mobile.login');
    }
}
