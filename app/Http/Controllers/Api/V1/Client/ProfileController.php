<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $client = Client::where('user_id', $request->user()->id)->firstOrFail();
        $locale = app()->getLocale();

        return $this->apiResponse([
            'id'          => $client->id,
            'name'        => $client->getTranslation('name', $locale, false) ?: $client->getTranslation('name', 'ar', false),
            'email'       => $client->email,
            'phone'       => $client->phone,
            'client_type' => $client->client_type,
            'national_id' => $client->national_id,
            'address'     => $client->getTranslation('address', $locale, false) ?: $client->getTranslation('address', 'ar', false),
            'is_active'   => $client->is_active,
        ], __('messages.success'));
    }
}
