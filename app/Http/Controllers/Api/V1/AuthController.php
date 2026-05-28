<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => __('auth.failed'),
                'data'    => null,
                'errors'  => ['credentials' => [__('auth.failed')]],
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('auth.deactivated'),
                'data'    => null,
                'errors'  => ['account' => ['الحساب غير مفعل']],
            ], 403);
        }

        if ($user->two_factor_confirmed_at && $request->has('two_factor_code')) {
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->two_factor_code);

            if (! $valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'رمز التحقق الثنائي غير صحيح',
                    'data'    => null,
                    'errors'  => ['two_factor_code' => ['رمز التحقق غير صحيح']],
                ], 422);
            }
        } elseif ($user->two_factor_confirmed_at) {
            return response()->json([
                'success' => false,
                'message' => 'مطلوب رمز التحقق الثنائي',
                'data'    => ['requires_2fa' => true],
                'errors'  => [],
            ], 200);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data'    => [
                'token' => $token,
                'user'  => $this->userResource($user),
            ],
            'errors'  => [],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
            'data'    => null,
            'errors'  => [],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $this->userResource($request->user()),
            'errors'  => [],
        ]);
    }

    public function enable2fa(Request $request): JsonResponse
    {
        $google2fa = new Google2FA();
        $secret    = $google2fa->generateSecretKey();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $request->user()->email,
            $secret
        );

        $request->user()->update(['two_factor_secret' => encrypt($secret)]);

        return response()->json([
            'success' => true,
            'message' => 'تم توليد رمز التحقق الثنائي',
            'data'    => ['qr_code_url' => $qrCodeUrl, 'secret' => $secret],
            'errors'  => [],
        ]);
    }

    public function verify2fa(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $user      = $request->user();
        $google2fa = new Google2FA();
        $valid     = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code);

        if (! $valid) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح',
                'data'    => null,
                'errors'  => ['code' => ['الرمز غير صحيح أو منتهي الصلاحية']],
            ], 422);
        }

        $user->update(['two_factor_confirmed_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'تم تفعيل التحقق الثنائي بنجاح',
            'data'    => null,
            'errors'  => [],
        ]);
    }

    public function disable2fa(Request $request): JsonResponse
    {
        $request->user()->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعطيل التحقق الثنائي',
            'data'    => null,
            'errors'  => [],
        ]);
    }

    private function userResource(User $user): array
    {
        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'avatar'   => $user->avatar,
            'language' => $user->language,
            'theme'    => $user->theme,
            'office_id' => $user->office_id,
            'roles'    => $user->getRoleNames(),
        ];
    }
}
