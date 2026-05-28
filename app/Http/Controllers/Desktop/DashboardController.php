<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $officeId = auth()->user()->office_id;

        $stats = [
            'cases'    => LegalCase::count(),
            'clients'  => Client::count(),
            'hearings' => Hearing::where('status', 'scheduled')
                ->where('scheduled_at', '>=', now())
                ->count(),
            'revenue'  => Invoice::where('status', 'paid')->sum('total_amount'),
        ];

        $upcomingHearings = Hearing::with(['legalCase', 'legalCase.client'])
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $recentCases = LegalCase::with('client')
            ->latest()
            ->limit(5)
            ->get();

        $monthlyRevenue = Invoice::where('status', 'paid')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('MONTH(updated_at) as month'),
                DB::raw('YEAR(updated_at) as year'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('desktop.dashboard', compact('stats', 'upcomingHearings', 'recentCases', 'monthlyRevenue'));
    }
}
