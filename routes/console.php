<?php

use App\Jobs\CheckOverdueInvoicesJob;
use App\Jobs\DispatchHearingRemindersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run daily at 8:00 AM — send hearing reminders for next 24 hours
Schedule::job(new DispatchHearingRemindersJob(24))->dailyAt('08:00');

// Run daily at midnight — mark overdue invoices and notify admins
Schedule::job(new CheckOverdueInvoicesJob())->dailyAt('00:05');
