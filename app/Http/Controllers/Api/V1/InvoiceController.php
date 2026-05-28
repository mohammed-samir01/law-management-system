<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['client', 'legalCase'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->client_id, fn($q, $id) => $q->where('client_id', $id))
            ->latest();

        $paginator = $query->paginate(15);

        return $this->apiResponse(
            InvoiceResource::collection($paginator),
            __('messages.success'),
            true,
            200,
            $paginator
        );
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['client', 'legalCase']);

        return $this->apiResponse(
            new InvoiceResource($invoice),
            __('messages.success')
        );
    }
}
