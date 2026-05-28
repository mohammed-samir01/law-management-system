<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\HearingResource;
use App\Models\Hearing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HearingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Hearing::with(['legalCase'])
            ->when($request->case_id, fn($q, $id) => $q->where('case_id', $id))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('scheduled_at');

        $paginator = $query->paginate(15);

        return $this->apiResponse(
            HearingResource::collection($paginator),
            __('messages.success'),
            true,
            200,
            $paginator
        );
    }

    public function upcoming(Request $request): JsonResponse
    {
        $paginator = Hearing::with(['legalCase'])
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
