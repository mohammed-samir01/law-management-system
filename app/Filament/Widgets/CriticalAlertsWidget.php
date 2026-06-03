<?php

namespace App\Filament\Widgets;

use App\Contracts\CriticalDeadlineProvider;
use App\Models\Hearing;
use App\Models\Task;
use Filament\Widgets\Widget;

class CriticalAlertsWidget extends Widget
{
    protected static string $view = 'filament.widgets.critical-alerts';
    protected static ?int $sort = 0; // top of the dashboard
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user !== null && $user->office_id && ! $user->hasRole('super_admin');
    }

    protected function getViewData(): array
    {
        return ['items' => $this->collectItems()];
    }

    /**
     * Aggregate imminent hearings + overdue tasks + any registered deadline
     * provider into a single, severity-sorted list. Degrades gracefully when
     * the deadline addon is absent.
     */
    private function collectItems(): array
    {
        $items = [];

        // Imminent hearings (next 7 days) — office scope applies via the model.
        $hearings = Hearing::query()
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_at', [now(), now()->addDays(7)])
            ->with('legalCase')
            ->orderBy('scheduled_at')
            ->limit(15)
            ->get();

        foreach ($hearings as $h) {
            $items[] = [
                'title'    => 'جلسة — ' . ($h->legalCase?->case_number ?? '—'),
                'due_date' => $h->scheduled_at,
                'url'      => $h->case_id ? url('/admin/legal-cases/' . $h->case_id) : null,
                'severity' => $h->scheduled_at && $h->scheduled_at->isToday() ? 'high' : 'medium',
            ];
        }

        // Overdue tasks.
        $tasks = Task::query()
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->with('legalCase')
            ->orderBy('due_date')
            ->limit(15)
            ->get();

        foreach ($tasks as $t) {
            $items[] = [
                'title'    => 'مهمة متأخرة — ' . $t->title,
                'due_date' => $t->due_date,
                'url'      => $t->case_id ? url('/admin/legal-cases/' . $t->case_id) : null,
                'severity' => 'high',
            ];
        }

        // Optional: deadline-calculator addon provider (Phase 5).
        try {
            if (app()->bound(CriticalDeadlineProvider::class)) {
                $items = array_merge($items, app(CriticalDeadlineProvider::class)->criticalItems());
            }
        } catch (\Throwable) {
            // never break the dashboard if a provider misbehaves
        }

        // Sort: high severity first, then soonest due date.
        usort($items, function ($a, $b) {
            $sev = ['high' => 0, 'medium' => 1, 'low' => 2];
            $cmp = ($sev[$a['severity']] ?? 9) <=> ($sev[$b['severity']] ?? 9);
            if ($cmp !== 0) return $cmp;
            return ($a['due_date']?->timestamp ?? PHP_INT_MAX) <=> ($b['due_date']?->timestamp ?? PHP_INT_MAX);
        });

        return array_slice($items, 0, 12);
    }
}
