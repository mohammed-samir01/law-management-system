<?php

namespace App\Http\Controllers\Mobile\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Hearing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HearingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Hearing::with('legalCase');

        if ($request->get('filter', 'upcoming') === 'upcoming') {
            $query->where('status', 'scheduled')->where('scheduled_at', '>=', now());
        }

        $hearings = $query->orderBy('scheduled_at')->paginate(15)->withQueryString();

        return view('mobile.lawyer.hearings.index', compact('hearings'));
    }
}
