<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PDFService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoicePdfController extends Controller
{
    public function show(int $id): StreamedResponse
    {
        $invoice = Invoice::with(['client', 'legalCase', 'office'])->findOrFail($id);

        return app(PDFService::class)->download($invoice);
    }
}
