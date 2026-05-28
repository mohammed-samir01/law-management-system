<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LegalCaseDetailResource;
use App\Http\Resources\Api\LegalCaseResource;
use App\Models\Client;
use App\Models\LegalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    private function getClient(Request $request): Client
    {
        return Client::where('user_id', $request->user()->id)->firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $client = $this->getClient($request);

        $paginator = LegalCase::with(['client'])
            ->where('client_id', $client->id)
            ->latest()
            ->paginate(15);

        return $this->apiResponse(
            LegalCaseResource::collection($paginator),
            __('messages.success'),
            true,
            200,
            $paginator
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $client = $this->getClient($request);

        $case = LegalCase::with(['client', 'lawyers', 'hearings' => fn($q) => $q->orderBy('scheduled_at'), 'documents'])
            ->where('client_id', $client->id)
            ->findOrFail($id);

        return $this->apiResponse(
            new LegalCaseDetailResource($case),
            __('messages.success')
        );
    }
}
