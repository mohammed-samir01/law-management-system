<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Hearing;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HearingController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        if (! $client) {
            return view('portal.hearings.index', ['hearings' => collect(), 'past' => collect()]);
        }

        $caseIds = LegalCase::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->pluck('id');

        $upcoming = Hearing::withoutGlobalScopes()
            ->whereIn('case_id', $caseIds)
            ->where('scheduled_at', '>=', now())
            ->with('legalCase')
            ->orderBy('scheduled_at')
            ->get();

        $past = Hearing::withoutGlobalScopes()
            ->whereIn('case_id', $caseIds)
            ->where('scheduled_at', '<', now())
            ->with('legalCase')
            ->orderByDesc('scheduled_at')
            ->limit(30)
            ->get();

        return view('portal.hearings.index', compact('upcoming', 'past'));
    }
}
