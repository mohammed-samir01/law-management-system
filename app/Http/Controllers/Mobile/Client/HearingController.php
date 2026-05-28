<?php

namespace App\Http\Controllers\Mobile\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hearing;
use App\Models\LegalCase;
use Illuminate\View\View;

class HearingController extends Controller
{
    public function index(): View
    {
        $client  = Client::where('user_id', auth()->id())->firstOrFail();
        $caseIds = LegalCase::where('client_id', $client->id)->pluck('id');

        $hearings = Hearing::with('legalCase')
            ->whereIn('case_id', $caseIds)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->paginate(15);

        return view('mobile.client.hearings.index', compact('hearings'));
    }
}
