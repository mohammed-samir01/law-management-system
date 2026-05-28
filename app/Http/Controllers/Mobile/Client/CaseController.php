<?php

namespace App\Http\Controllers\Mobile\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LegalCase;
use Illuminate\View\View;

class CaseController extends Controller
{
    private function getClient(): Client
    {
        return Client::where('user_id', auth()->id())->firstOrFail();
    }

    public function index(): View
    {
        $client = $this->getClient();

        $cases = LegalCase::where('client_id', $client->id)
            ->latest()
            ->paginate(15);

        return view('mobile.client.cases.index', compact('cases'));
    }

    public function show(int $id): View
    {
        $client = $this->getClient();

        $case = LegalCase::with([
            'hearings' => fn($q) => $q->orderBy('scheduled_at'),
        ])
            ->where('client_id', $client->id)
            ->findOrFail($id);

        return view('mobile.client.cases.show', compact('case'));
    }
}
