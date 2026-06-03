<?php

namespace App\Services\Court;

class CourtSyncService
{
    /**
     * Resolve the active court provider. Defaults to the stub until a real
     * integration (with credentials) is registered.
     */
    public function provider(): CourtProviderInterface
    {
        return new StubCourtProvider();
    }

    public function fetchCaseStatus(string $caseNumber): array
    {
        return $this->provider()->fetchCaseStatus($caseNumber);
    }
}
