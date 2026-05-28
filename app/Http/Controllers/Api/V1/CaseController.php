<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LegalCaseDetailResource;
use App\Http\Resources\Api\LegalCaseResource;
use App\Models\LegalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LegalCase::with(['client'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('case_number', 'like', "%{$search}%")
                      ->orWhereJsonContains('title->ar', $search)
                      ->orWhereJsonContains('title->en', $search);
                });
            })
            ->latest();

        $paginator = $query->paginate(15);

        return $this->apiResponse(
            LegalCaseResource::collection($paginator),
            __('messages.success'),
            true,
            200,
            $paginator
        );
    }

    public function show(LegalCase $case): JsonResponse
    {
        $case->load(['client', 'lawyers', 'hearings' => fn($q) => $q->orderBy('scheduled_at'), 'documents']);

        return $this->apiResponse(
            new LegalCaseDetailResource($case),
            __('messages.success')
        );
    }
}
