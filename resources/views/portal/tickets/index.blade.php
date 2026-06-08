@extends('portal.layouts.app')

@section('title', __('portal.my_tickets'))
@section('hero-title', __('portal.my_tickets'))
@section('hero-subtitle', __('portal.tickets_subtitle'))

@section('content')

<div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem;">
    <a href="{{ route('portal.tickets.create') }}" class="btn btn-gold">
        + {{ __('portal.new_ticket') }}
    </a>
</div>

@if($tickets->isEmpty())
    <div class="empty">
        <div class="empty-icon">🎫</div>
        <p>{{ __('portal.no_tickets') }}</p>
        <a href="{{ route('portal.tickets.create') }}" class="btn btn-primary" style="margin-top:1rem;display:inline-flex;">
            {{ __('portal.new_ticket') }}
        </a>
    </div>
@else
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('portal.ticket_title') }}</th>
                    <th>{{ __('portal.priority') }}</th>
                    <th>{{ __('portal.status') }}</th>
                    <th>{{ __('portal.date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td style="color:#94a3b8;font-size:.82rem;">{{ $ticket->id }}</td>
                    <td style="font-weight:600;">{{ $ticket->title }}</td>
                    <td>
                        <span class="badge badge-{{ $ticket->priority }}">
                            {{ __('portal.priority_' . $ticket->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
                    </td>
                    <td style="color:#64748b;">{{ $ticket->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('portal.tickets.show', $ticket->id) }}" class="btn btn-outline btn-sm">
                            {{ __('portal.view') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
