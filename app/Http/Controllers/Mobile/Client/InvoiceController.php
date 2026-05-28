<?php

namespace App\Http\Controllers\Mobile\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $client = Client::where('user_id', auth()->id())->firstOrFail();

        $invoices = Invoice::with('legalCase')
            ->where('client_id', $client->id)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('mobile.client.invoices.index', compact('invoices'));
    }

    public function show(int $id): View
    {
        $client = Client::where('user_id', auth()->id())->firstOrFail();

        $invoice = Invoice::with(['legalCase', 'office'])
            ->where('client_id', $client->id)
            ->findOrFail($id);

        return view('mobile.client.invoices.show', compact('invoice'));
    }
}
