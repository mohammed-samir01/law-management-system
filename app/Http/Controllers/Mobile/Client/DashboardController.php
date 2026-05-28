<?php

namespace App\Http\Controllers\Mobile\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $client = Client::where('user_id', auth()->id())->firstOrFail();

        $caseIds = LegalCase::where('client_id', $client->id)->pluck('id');

        $stats = [
            'cases_total'       => $caseIds->count(),
            'cases_active'      => LegalCase::where('client_id', $client->id)->where('status', 'active')->count(),
            'hearings_upcoming' => Hearing::whereIn('case_id', $caseIds)->where('status', 'scheduled')->where('scheduled_at', '>=', now())->count(),
            'invoices_unpaid'   => Invoice::where('client_id', $client->id)->whereIn('status', ['sent', 'overdue'])->count(),
        ];

        $upcomingHearings = Hearing::with('legalCase')
            ->whereIn('case_id', $caseIds)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return view('mobile.client.dashboard', compact('client', 'stats', 'upcomingHearings'));
    }
}
