<?php

namespace App\Services;

use App\Models\CaseDeadline;
use App\Models\LegalCase;
use Illuminate\Support\Carbon;

class DeadlineCalculatorService
{
    /**
     * Available deadline types for a jurisdiction (with their day counts).
     *
     * @return array<string,int>
     */
    public function typesFor(?string $jurisdiction): array
    {
        $jurisdiction = in_array($jurisdiction, ['eg', 'sa'], true) ? $jurisdiction : 'eg';

        return config("legal_deadlines.rules.{$jurisdiction}", []);
    }

    public function durationFor(?string $jurisdiction, string $type): int
    {
        $jurisdiction = in_array($jurisdiction, ['eg', 'sa'], true) ? $jurisdiction : 'eg';

        return (int) config(
            "legal_deadlines.rules.{$jurisdiction}.{$type}",
            config('legal_deadlines.fallback_days', 30)
        );
    }

    /**
     * Compute and persist a deadline for a case from a basis (verdict) date.
     */
    public function computeFor(LegalCase $case, Carbon $basisDate, string $type, ?string $jurisdiction = null): CaseDeadline
    {
        $duration = $this->durationFor($jurisdiction, $type);

        return CaseDeadline::create([
            'office_id'     => $case->office_id,
            'case_id'       => $case->id,
            'type'          => $type,
            'jurisdiction'  => in_array($jurisdiction, ['eg', 'sa'], true) ? $jurisdiction : null,
            'basis_date'    => $basisDate->toDateString(),
            'due_date'      => $basisDate->copy()->addDays($duration)->toDateString(),
            'duration_days' => $duration,
            'status'        => 'open',
            'alert_offsets' => config('legal_deadlines.default_offsets', [30, 14, 7, 3, 1]),
        ]);
    }
}
