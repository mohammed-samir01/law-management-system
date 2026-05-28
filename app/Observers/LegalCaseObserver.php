<?php

namespace App\Observers;

use App\Models\LegalCase;
use App\Services\NotificationService;

class LegalCaseObserver
{
    public function updating(LegalCase $case): void
    {
        // Store old status before save for comparison in updated()
        if ($case->isDirty('status')) {
            $case->_oldStatus = $case->getOriginal('status');
        }
    }

    public function updated(LegalCase $case): void
    {
        if (isset($case->_oldStatus) && $case->_oldStatus !== $case->status) {
            $case->loadMissing('lawyers');
            NotificationService::onCaseStatusChanged($case, $case->_oldStatus);
        }
    }
}
