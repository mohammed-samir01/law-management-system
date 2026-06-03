<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\LegalCase;
use App\Models\Payment;
use Carbon\Carbon;

class ReportService
{
    /**
     * Financial summary within a date range, scoped to the given office.
     *
     * @return array{rows: array<int,array{label:string,value:string}>, title:string}
     */
    public function financial(int $officeId, Carbon $from, Carbon $to): array
    {
        $invoices = Invoice::withoutGlobalScopes()->where('office_id', $officeId)
            ->whereBetween('created_at', [$from, $to]);

        $payments = Payment::withoutGlobalScopes()->where('office_id', $officeId)
            ->whereBetween('created_at', [$from, $to]);

        $totalInvoiced = (clone $invoices)->sum('total_amount');
        $totalPaid     = (clone $invoices)->where('status', 'paid')->sum('total_amount');
        $outstanding   = (clone $invoices)->whereIn('status', ['sent', 'overdue'])->sum('total_amount');
        $collected     = (clone $payments)->where('status', 'completed')->sum('amount');

        return [
            'title' => __('addons.reports_financial'),
            'rows'  => [
                ['label' => 'إجمالي الفواتير',   'value' => number_format((float) $totalInvoiced, 2)],
                ['label' => 'المدفوع',           'value' => number_format((float) $totalPaid, 2)],
                ['label' => 'المستحق',           'value' => number_format((float) $outstanding, 2)],
                ['label' => 'المحصّل (مدفوعات)', 'value' => number_format((float) $collected, 2)],
            ],
        ];
    }

    /**
     * Case counts grouped by status and by type.
     */
    public function cases(int $officeId, Carbon $from, Carbon $to): array
    {
        $base = LegalCase::withoutGlobalScopes()->where('office_id', $officeId)
            ->whereBetween('created_at', [$from, $to]);

        $byStatus = (clone $base)->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');
        $byType   = (clone $base)->selectRaw('type, COUNT(*) c')->groupBy('type')->pluck('c', 'type');

        $rows = [];
        foreach ($byStatus as $status => $count) {
            $rows[] = ['label' => 'الحالة: ' . $status, 'value' => (string) $count];
        }
        foreach ($byType as $type => $count) {
            $rows[] = ['label' => 'النوع: ' . $type, 'value' => (string) $count];
        }

        return ['title' => __('addons.reports_cases'), 'rows' => $rows];
    }

    /**
     * Lawyer performance — number of cases assigned per lawyer.
     */
    public function lawyers(int $officeId, Carbon $from, Carbon $to): array
    {
        $cases = LegalCase::withoutGlobalScopes()->where('office_id', $officeId)
            ->whereBetween('created_at', [$from, $to])
            ->with('lawyers')
            ->get();

        $counts = [];
        foreach ($cases as $case) {
            foreach ($case->lawyers as $lawyer) {
                $counts[$lawyer->name] = ($counts[$lawyer->name] ?? 0) + 1;
            }
        }

        arsort($counts);

        $rows = [];
        foreach ($counts as $name => $count) {
            $rows[] = ['label' => $name, 'value' => (string) $count];
        }

        return ['title' => __('addons.reports_lawyers'), 'rows' => $rows];
    }

    public function generate(string $type, int $officeId, Carbon $from, Carbon $to): array
    {
        return match ($type) {
            'cases'   => $this->cases($officeId, $from, $to),
            'lawyers' => $this->lawyers($officeId, $from, $to),
            default   => $this->financial($officeId, $from, $to),
        };
    }
}
