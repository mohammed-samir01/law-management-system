<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = SupportTicket::withoutGlobalScopes()
            ->where('created_by', $request->user()->id)
            ->latest()
            ->get();

        return view('portal.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('portal.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
            'priority'    => ['required', 'in:urgent,normal,low'],
        ]);

        $user   = $request->user();
        $office = $user->office;

        SupportTicket::create([
            'office_id'   => $office?->id,
            'title'       => $data['title'],
            'description' => $data['description'],
            'priority'    => $data['priority'],
            'status'      => 'open',
            'created_by'  => $user->id,
        ]);

        return redirect()->route('portal.tickets.index')
            ->with('success', __('portal.ticket_created'));
    }

    public function show(Request $request, int $id): View
    {
        $ticket = SupportTicket::withoutGlobalScopes()
            ->where('created_by', $request->user()->id)
            ->with(['replies.user'])
            ->findOrFail($id);

        return view('portal.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, int $id): RedirectResponse
    {
        $ticket = SupportTicket::withoutGlobalScopes()
            ->where('created_by', $request->user()->id)
            ->findOrFail($id);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:3000'],
        ]);

        TicketReply::withoutGlobalScopes()->create([
            'ticket_id' => $ticket->id,
            'user_id'   => $request->user()->id,
            'content'   => $data['content'],
        ]);

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'reopened']);
        }

        return redirect()->route('portal.tickets.show', $id)
            ->with('success', __('portal.reply_sent'));
    }
}
