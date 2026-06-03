<?php

namespace App\Services\Court;

interface CourtProviderInterface
{
    /**
     * Fetch case status / next hearing from a court portal.
     *
     * @return array{configured: bool, message: string, data: array}
     */
    public function fetchCaseStatus(string $caseNumber): array;

    public function isConfigured(): bool;
}
