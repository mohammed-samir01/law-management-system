<?php

namespace App\Services\Deadlines;

use App\Contracts\CriticalDeadlineProvider;
use App\Models\CaseDeadline;

/**
 * Feeds open legal deadlines into CriticalAlertsWidget. Returns nothing unless
 * the current office has the legal-deadlines addon active, so the widget stays
 * correct whether or not the addon is purchased.
 */
class DeadlineCriticalProvider implements CriticalDeadlineProvider
{
    public function criticalItems(): array
    {
        $office = auth()->user()?->office;

        if (! $office || ! $office->hasAddon('legal-deadlines')) {
            return [];
        }

        // Office global scope applies (authenticated context).
        return CaseDeadline::query()
            ->where('status', 'open')
            ->whereBetween('due_date', [today(), today()->addDays(14)])
            ->with('legalCase')
            ->orderBy('due_date')
            ->limit(15)
            ->get()
            ->map(fn (CaseDeadline $d) => [
                'title'    => __('deadlines.type_' . $d->type) . ' — ' . ($d->legalCase?->case_number ?? '—'),
                'due_date' => $d->due_date,
                'url'      => $d->case_id ? url('/admin/legal-cases/' . $d->case_id) : null,
                'severity' => ($d->due_date && today()->diffInDays($d->due_date, false) <= 3) ? 'high' : 'medium',
            ])
            ->all();
    }
}
