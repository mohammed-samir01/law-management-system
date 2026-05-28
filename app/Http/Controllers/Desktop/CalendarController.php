<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\Hearing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year',  now()->year);

        $hearings = Hearing::with(['legalCase', 'legalCase.client'])
            ->whereMonth('scheduled_at', $month)
            ->whereYear('scheduled_at',  $year)
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn($h) => $h->scheduled_at->format('Y-m-d'));

        $upcoming = Hearing::with(['legalCase'])
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        return view('desktop.calendar', compact('hearings', 'upcoming', 'month', 'year'));
    }
}
