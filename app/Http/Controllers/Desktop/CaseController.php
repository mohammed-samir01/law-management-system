<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function index(Request $request): View
    {
        $query = LegalCase::with(['client', 'hearings'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_EXTRACT(title, '$.ar') LIKE ?", ["%{$search}%"]);
            });
        }

        $cases = $query->paginate(15)->withQueryString();

        return view('desktop.cases.index', compact('cases'));
    }

    public function show(int $id): View
    {
        $case = LegalCase::with(['client', 'hearings', 'documents', 'invoices'])->findOrFail($id);

        return view('desktop.cases.show', compact('case'));
    }
}
