<?php

namespace App\Observers;

use App\Models\Document;
use App\Services\NotificationService;

class DocumentObserver
{
    public function created(Document $document): void
    {
        if ($document->status === 'final') {
            NotificationService::onDocumentUploaded($document);
        }
    }
}
