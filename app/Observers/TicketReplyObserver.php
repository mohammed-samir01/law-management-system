<?php

namespace App\Observers;

use App\Models\TicketReply;
use App\Services\NotificationService;

class TicketReplyObserver
{
    public function created(TicketReply $reply): void
    {
        $reply->loadMissing('ticket');
        NotificationService::onTicketReplied($reply->ticket, $reply);
    }
}
