<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\HearingResource;
use App\Models\Client;
use App\Models\Hearing;
use App\Models\LegalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HearingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = Client::where('user_id', $request->user()->id)->firstOrFail();

        $caseIds = LegalCase::where('client_id', $client->id)->pluck('id');

        $paginator = Hearing::with(['legalCase'])
            ->whereIn('case_id', $caseIds)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->paginate(15);

        return $this->apiResponse(
            HearingResource::collection($paginator),
            __('messages.success'),
            true,
            200,
            $paginator
        );
    }
}
