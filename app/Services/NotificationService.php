<?php

namespace App\Services;

use App\Models\Document;
use App\Models\LegalCase;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\CaseStatusChangedNotification;
use App\Notifications\DocumentUploadedNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\TicketRepliedNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public static function onCaseStatusChanged(LegalCase $case, string $oldStatus): void
    {
        if ($oldStatus === $case->status) {
            return;
        }

        // Notify all assigned lawyers
        foreach ($case->lawyers()->withoutGlobalScopes()->get() as $lawyer) {
            $lawyer->notify(new CaseStatusChangedNotification($case, $oldStatus, $case->status));
        }

        // Notify office admins
        self::notifyOfficeAdmins(
            $case->office_id,
            new CaseStatusChangedNotification($case, $oldStatus, $case->status)
        );
    }

    public static function onPaymentReceived(Payment $payment): void
    {
        self::notifyOfficeAdmins(
            $payment->office_id,
            new PaymentReceivedNotification($payment)
        );
    }

    public static function onDocumentUploaded(Document $document): void
    {
        self::notifyOfficeAdmins(
            $document->office_id,
            new DocumentUploadedNotification($document)
        );
    }

    public static function onTicketReplied(SupportTicket $ticket, TicketReply $reply): void
    {
        // Notify the ticket creator (if it's not the one replying)
        if ($ticket->createdBy && $ticket->createdBy->id !== $reply->user_id) {
            $ticket->createdBy->notify(new TicketRepliedNotification($ticket, $reply));
        }

        // Notify assigned staff (if it's not the one replying)
        if ($ticket->assignedTo && $ticket->assignedTo->id !== $reply->user_id) {
            $ticket->assignedTo->notify(new TicketRepliedNotification($ticket, $reply));
        }
    }

    private static function notifyOfficeAdmins(int $officeId, $notification): void
    {
        try {
            User::withoutGlobalScopes()
                ->where('office_id', $officeId)
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['office_admin', 'super_admin']))
                ->each(fn ($admin) => $admin->notify($notification));
        } catch (\Throwable $e) {
            Log::error('NotificationService::notifyOfficeAdmins failed', ['error' => $e->getMessage()]);
        }
    }
}
