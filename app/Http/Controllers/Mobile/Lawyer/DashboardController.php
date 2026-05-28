<?php

namespace App\Http\Controllers\Mobile\Lawyer;

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
        $stats = [
            'cases' => [
                'total'   => LegalCase::count(),
                'active'  => LegalCase::where('status', 'active')->count(),
                'new'     => LegalCase::where('status', 'new')->count(),
            ],
            'hearings' => [
                'today'    => Hearing::where('status', 'scheduled')->whereDate('scheduled_at', today())->count(),
                'upcoming' => Hearing::where('status', 'scheduled')->where('scheduled_at', '>=', now())->count(),
            ],
            'clients'  => ['total' => Client::count()],
            'invoices' => ['unpaid' => Invoice::whereIn('status', ['sent', 'overdue'])->count()],
        ];

        $upcomingHearings = Hearing::with('legalCase')
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return view('mobile.lawyer.dashboard', compact('stats', 'upcomingHearings'));
    }
}
