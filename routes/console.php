<?php

use App\Jobs\CheckOverdueInvoicesJob;
use App\Jobs\DispatchDeadlineAlertsJob;
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

// Run daily — expire subscriptions, suspend offices, send 3-day expiry warnings
Schedule::command('subscriptions:enforce')->dailyAt('00:10');

// Run daily — expire add-on subscriptions and send warnings
Schedule::command('addons:enforce')->dailyAt('00:20');

// Run daily — send 7-day expiry warning
Schedule::command('subscriptions:warn --days=7')->dailyAt('00:15');

// Run daily at 07:30 — alert on upcoming legal deadlines (legal-deadlines addon)
Schedule::job(new DispatchDeadlineAlertsJob())->dailyAt('07:30')->withoutOverlapping();
