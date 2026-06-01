<?php

namespace App\Services;

use App\Models\AIResult;
use App\Models\Office;
use Carbon\CarbonImmutable;
use RuntimeException;

class AIUsageService
{
    /**
     * The current usage window for an office: the active subscription period,
     * the trial window, or the current calendar month as a fallback.
     *
     * @return array{0: \Carbon\CarbonInterface, 1: \Carbon\CarbonInterface}
     */
    public function period(Office $office): array
    {
        $sub = $office->subscription;

        if ($sub && $sub->status === 'active' && $sub->current_period_start && $sub->current_period_end) {
            return [$sub->current_period_start, $sub->current_period_end];
        }

        if ($sub && $sub->onTrial() && $sub->trial_ends_at) {
            return [$sub->trial_ends_at->copy()->subDays(30), $sub->trial_ends_at];
        }

        $now = CarbonImmutable::now();

        return [$now->startOfMonth(), $now->endOfMonth()];
    }

    public function requestsUsed(Office $office): int
    {
        [$start, $end] = $this->period($office);

        return AIResult::withoutGlobalScopes()
            ->where('office_id', $office->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    public function tokensUsed(Office $office): int
    {
        [$start, $end] = $this->period($office);

        return (int) AIResult::withoutGlobalScopes()
            ->where('office_id', $office->id)
            ->whereBetween('created_at', [$start, $end])
            ->sum('tokens_used');
    }

    public function requestsLimit(Office $office): ?int
    {
        return $office->activePlan()?->max_ai_requests_monthly; // null = unlimited
    }

    public function tokensLimit(Office $office): ?int
    {
        return $office->activePlan()?->max_ai_tokens_monthly;
    }

    public function aiEnabled(Office $office): bool
    {
        return (bool) ($office->activePlan()?->ai_enabled ?? false);
    }

    public function remainingRequests(Office $office): ?int
    {
        $limit = $this->requestsLimit($office);

        return $limit === null ? null : max(0, $limit - $this->requestsUsed($office));
    }

    public function exceeded(Office $office): bool
    {
        $reqLimit = $this->requestsLimit($office);
        if ($reqLimit !== null && $this->requestsUsed($office) >= $reqLimit) {
            return true;
        }

        $tokLimit = $this->tokensLimit($office);
        if ($tokLimit !== null && $this->tokensUsed($office) >= $tokLimit) {
            return true;
        }

        return false;
    }

    /**
     * Throws a friendly Arabic message if AI is disabled or quota exceeded.
     */
    public function assertAllowed(Office $office): void
    {
        if (! $this->aiEnabled($office)) {
            throw new RuntimeException('خطتك الحالية لا تشمل ميزة الذكاء الاصطناعي. يرجى الترقية.');
        }

        if ($this->exceeded($office)) {
            $limit = $this->requestsLimit($office);
            throw new RuntimeException("بلغت حد الذكاء الاصطناعي لهذه الفترة ({$this->requestsUsed($office)}/{$limit}). يرجى الترقية أو الانتظار للفترة التالية.");
        }
    }
}
