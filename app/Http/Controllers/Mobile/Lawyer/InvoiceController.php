<?php

namespace App\Http\Controllers\Mobile\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::with(['client', 'legalCase'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('mobile.lawyer.invoices.index', compact('invoices'));
    }

    public function show(int $id): View
    {
        $invoice = Invoice::with(['client', 'legalCase', 'office'])->findOrFail($id);

        return view('mobile.lawyer.invoices.show', compact('invoice'));
    }
}
