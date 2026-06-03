<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InstallmentService
{
    /**
     * Split an invoice total into $count installments starting $firstDue,
     * spaced $intervalDays apart. The last installment absorbs rounding.
     */
    public function createPlan(Invoice $invoice, int $count, Carbon $firstDue, int $intervalDays): InstallmentPlan
    {
        $count = max(2, $count);
        $total = (float) $invoice->total_amount;
        $base  = floor(($total / $count) * 100) / 100; // round down to 2dp

        return DB::transaction(function () use ($invoice, $count, $firstDue, $intervalDays, $total, $base) {
            $plan = InstallmentPlan::create([
                'office_id'    => $invoice->office_id,
                'invoice_id'   => $invoice->id,
                'total_amount' => $total,
                'count'        => $count,
                'status'       => 'active',
            ]);

            $allocated = 0.0;
            for ($i = 1; $i <= $count; $i++) {
                $amount = ($i === $count) ? round($total - $allocated, 2) : $base;
                $allocated += $amount;

                Installment::create([
                    'office_id'           => $invoice->office_id,
                    'installment_plan_id' => $plan->id,
                    'sequence'            => $i,
                    'amount'              => $amount,
                    'due_date'            => (clone $firstDue)->addDays($intervalDays * ($i - 1)),
                    'status'              => 'pending',
                ]);
            }

            // Keep the parent invoice out of the overdue sweep while a plan is active.
            if ($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent']);
            }

            return $plan;
        });
    }
}
