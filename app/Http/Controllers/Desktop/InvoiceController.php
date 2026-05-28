<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PDFService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['client', 'legalCase'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $invoices = $query->paginate(15)->withQueryString();

        $totals = [
            'paid'    => Invoice::where('status', 'paid')->sum('total_amount'),
            'pending' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
        ];

        return view('desktop.invoices.index', compact('invoices', 'totals'));
    }

    public function pdf(int $id): StreamedResponse
    {
        $invoice = Invoice::with(['client', 'legalCase', 'office'])->findOrFail($id);

        return app(PDFService::class)->download($invoice);
    }
}
