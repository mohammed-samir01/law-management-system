<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\InvoiceResource;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = Client::where('user_id', $request->user()->id)->firstOrFail();

        $paginator = Invoice::with(['legalCase'])
            ->where('client_id', $client->id)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        return $this->apiResponse(
            InvoiceResource::collection($paginator),
            __('messages.success'),
            true,
            200,
            $paginator
        );
    }
}
