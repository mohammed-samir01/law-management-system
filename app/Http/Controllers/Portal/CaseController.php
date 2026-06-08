<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Hearing;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $cases = $client
            ? LegalCase::withoutGlobalScopes()
                ->where('client_id', $client->id)
                ->latest()
                ->get()
            : collect();

        return view('portal.cases.index', compact('cases'));
    }

    public function show(Request $request, int $id): View
    {
        $client = $request->user()->client;

        $case = LegalCase::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->findOrFail($id);

        $hearings = Hearing::withoutGlobalScopes()
            ->where('case_id', $case->id)
            ->orderByDesc('scheduled_at')
            ->get();

        $documents = Document::withoutGlobalScopes()
            ->where('documentable_type', LegalCase::class)
            ->where('documentable_id', $case->id)
            ->latest()
            ->get();

        return view('portal.cases.show', compact('case', 'hearings', 'documents'));
    }
}
