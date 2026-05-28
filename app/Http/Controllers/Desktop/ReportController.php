<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $casesByStatus = LegalCase::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $monthlyRevenue = Invoice::where('status', 'paid')
            ->where('updated_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $hearingsByMonth = Hearing::where('scheduled_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(scheduled_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $totals = [
            'revenue_total'  => Invoice::where('status', 'paid')->sum('total_amount'),
            'pending_amount' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
            'active_cases'   => LegalCase::where('status', 'active')->count(),
            'total_hearings' => Hearing::count(),
        ];

        return view('desktop.reports.index', compact(
            'casesByStatus', 'monthlyRevenue', 'hearingsByMonth', 'totals'
        ));
    }
}
