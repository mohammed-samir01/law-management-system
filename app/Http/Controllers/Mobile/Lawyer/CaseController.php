<?php

namespace App\Http\Controllers\Mobile\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function index(Request $request): View
    {
        $cases = LegalCase::with('client')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('case_number', 'like', "%{$search}%")
                      ->orWhereJsonContains('title->ar', $search);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('mobile.lawyer.cases.index', compact('cases'));
    }

    public function show(int $id): View
    {
        $case = LegalCase::with([
            'client',
            'lawyers',
            'hearings' => fn($q) => $q->orderBy('scheduled_at'),
            'documents',
        ])->findOrFail($id);

        return view('mobile.lawyer.cases.show', compact('case'));
    }
}
