<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $officeId = $request->user()->office_id;

        $data = Cache::remember("office_{$officeId}_dashboard_stats", 300, function () {
            return [
                'cases' => [
                    'total'    => LegalCase::count(),
                    'active'   => LegalCase::where('status', 'active')->count(),
                    'new'      => LegalCase::where('status', 'new')->count(),
                    'closed'   => LegalCase::where('status', 'closed')->count(),
                    'pending'  => LegalCase::where('status', 'pending')->count(),
                ],
                'hearings' => [
                    'upcoming' => Hearing::where('status', 'scheduled')
                        ->where('scheduled_at', '>=', now())
                        ->count(),
                    'today'    => Hearing::where('status', 'scheduled')
                        ->whereDate('scheduled_at', today())
                        ->count(),
                ],
                'clients' => [
                    'total'  => Client::count(),
                    'active' => Client::where('is_active', true)->count(),
                ],
                'invoices' => [
                    'unpaid'  => Invoice::whereIn('status', ['sent', 'overdue'])->count(),
                    'overdue' => Invoice::where('status', 'overdue')->count(),
                ],
            ];
        });

        return $this->apiResponse($data, __('messages.success'));
    }
}
