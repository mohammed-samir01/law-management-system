<?php

namespace App\Support;

class FieldAccess
{
    /**
     * Whether the current user may view monetary/financial values.
     * Assistants are excluded; everyone else (lawyer, office_admin, super_admin) may.
     * Used to gate amount fields/columns via Filament ->visible(...).
     */
    public static function financials(): bool
    {
        $user = auth()->user();

        return $user !== null && ! $user->hasRole('assistant');
    }
}
