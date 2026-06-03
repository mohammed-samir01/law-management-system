<?php

namespace App\Contracts;

/**
 * Shared foundation: a source of critical, time-sensitive items for the current
 * office, surfaced by CriticalAlertsWidget. The deadline-calculator addon
 * (Phase 5) binds an implementation into the container so it plugs in without
 * the widget hard-depending on it.
 */
interface CriticalDeadlineProvider
{
    /**
     * @return array<int, array{title: string, due_date: \Illuminate\Support\Carbon|\DateTimeInterface|null, url: ?string, severity: string}>
     */
    public function criticalItems(): array;
}
