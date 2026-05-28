<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushController extends Controller
{
    public function saveToken(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'platform' => 'nullable|in:android,ios,web',
        ]);

        DeviceToken::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'token'   => $request->token,
            ],
            [
                'platform' => $request->platform ?? 'android',
            ]
        );

        return response()->json(['success' => true]);
    }
}
