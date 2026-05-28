<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceDueNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckOverdueInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        // Find invoices due today or overdue (not yet marked overdue)
        $invoices = Invoice::withoutGlobalScopes()
            ->whereIn('status', ['sent', 'draft'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now())
            ->with(['office', 'client'])
            ->get();

        $count = 0;
        foreach ($invoices as $invoice) {
            // Mark as overdue
            $invoice->update(['status' => 'overdue']);

            // Notify office admins of this office
            $admins = User::withoutGlobalScopes()
                ->where('office_id', $invoice->office_id)
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['office_admin', 'super_admin']))
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new InvoiceDueNotification($invoice));
            }

            $count++;
        }

        Log::info("CheckOverdueInvoicesJob: processed {$count} overdue invoices.");
    }

    public function failed(\Throwable $e): void
    {
        Log::error('CheckOverdueInvoicesJob failed', ['error' => $e->getMessage()]);
    }
}
