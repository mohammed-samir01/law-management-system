<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ClientDetailResource;
use App\Http\Resources\Api\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Client::withCount('cases')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhereJsonContains('name->ar', $search)
                      ->orWhereJsonContains('name->en', $search);
                });
            })
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN)))
            ->latest();

        $paginator = $query->paginate(15);

        return $this->apiResponse(
            ClientResource::collection($paginator),
            __('messages.success'),
            true,
            200,
            $paginator
        );
    }

    public function show(Client $client): JsonResponse
    {
        $client->load(['cases' => fn($q) => $q->latest()->limit(20)]);

        return $this->apiResponse(
            new ClientDetailResource($client),
            __('messages.success')
        );
    }
}
