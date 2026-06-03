<?php

namespace App\Services\Court;

/**
 * Default provider. Court e-portals (KSA Najiz, Egypt e-litigation) require a
 * formal access agreement / credentials that aren't publicly available, so this
 * ships disabled. It never throws and never blocks core saves — it simply
 * reports "not configured" until a real provider is wired in.
 */
class StubCourtProvider implements CourtProviderInterface
{
    public function fetchCaseStatus(string $caseNumber): array
    {
        return [
            'configured' => false,
            'message'    => __('addons.court_not_configured'),
            'data'       => [],
        ];
    }

    public function isConfigured(): bool
    {
        return false;
    }
}
