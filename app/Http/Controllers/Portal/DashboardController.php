<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->user()->client ?? null;

        $cases = $client
            ? LegalCase::withoutGlobalScopes()->where('client_id', $client->id)->latest()->get()
            : collect();

        $upcomingHearings = $client
            ? Hearing::withoutGlobalScopes()
                ->whereIn('case_id', $cases->pluck('id'))
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get()
            : collect();

        $invoices = $client
            ? Invoice::withoutGlobalScopes()
                ->where('client_id', $client->id)
                ->latest()
                ->get()
            : collect();

        return view('portal.dashboard', compact('cases', 'upcomingHearings', 'invoices'));
    }
}
