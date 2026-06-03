<?php

namespace App\Http\Controllers;

use App\Models\Hearing;
use App\Models\Office;
use Illuminate\Http\Response;

class CalendarFeedController extends Controller
{
    /**
     * Public ICS feed (token-gated, no auth). Subscribe from Google/Apple
     * Calendar. Only serves data for offices with the calendar-sync addon.
     */
    public function feed(string $token): Response
    {
        $office = Office::where('settings->calendar_token', $token)->first();

        abort_unless($office && $office->hasAddon('calendar-sync'), 404);

        $hearings = Hearing::withoutGlobalScopes()
            ->where('office_id', $office->id)
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [now()->subMonths(1), now()->addMonths(6)])
            ->with('legalCase')
            ->orderBy('scheduled_at')
            ->limit(500)
            ->get();

        $ics = $this->build($office, $hearings);

        return response($ics, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="mizan.ics"');
    }

    private function build(Office $office, $hearings): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Mizan//Hearings//AR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:' . $this->esc($office->name . ' — الجلسات'),
        ];

        foreach ($hearings as $h) {
            $start = $h->scheduled_at;
            $end   = (clone $start)->addHour();
            $case  = $h->legalCase;

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:hearing-' . $h->id . '@mizan';
            $lines[] = 'DTSTAMP:' . now()->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTSTART:' . $start->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTEND:' . $end->utc()->format('Ymd\THis\Z');
            $lines[] = 'SUMMARY:' . $this->esc('جلسة ' . ($case?->case_number ?? ''));
            $lines[] = 'LOCATION:' . $this->esc($h->location ?? '');
            $lines[] = 'DESCRIPTION:' . $this->esc(trim(($case?->court ?? '') . ' ' . ($h->judge ? '— ' . $h->judge : '')));
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    private function esc(string $text): string
    {
        return str_replace(["\\", "\n", ",", ";"], ["\\\\", "\\n", "\\,", "\\;"], trim($text));
    }
}
