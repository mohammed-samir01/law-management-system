@extends('portal.layouts.app')

@section('title', __('portal.ticket') . ' #' . $ticket->id)
@section('hero-title', $ticket->title)
@section('hero-subtitle', __('portal.ticket') . ' #' . $ticket->id)

@push('styles')
<style>
    .container { max-width: 780px; }
    .back-link { display:inline-flex;align-items:center;gap:.4rem;color:#64748b;text-decoration:none;font-size:.88rem;margin-bottom:1.25rem; }
    .back-link:hover { color:#1E3A5F; }
</style>
@endpush

@section('content')

<a href="{{ route('portal.tickets.index') }}" class="back-link">← {{ __('portal.back_to_tickets') }}</a>

{{-- Ticket header --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid #f1f5f9;">
        <span style="font-size:1.05rem;font-weight:700;color:#1E3A5F;">{{ $ticket->title }}</span>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <span class="badge badge-{{ $ticket->priority }}">{{ __('portal.priority_' . $ticket->priority) }}</span>
            <span class="badge badge-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
        </div>
    </div>

    @if($ticket->description)
    <div style="font-size:.9rem;color:#475569;line-height:1.7;white-space:pre-wrap;">{{ $ticket->description }}</div>
    <div style="font-size:.76rem;color:#94a3b8;margin-top:.75rem;">
        {{ $ticket->created_at->format('Y/m/d H:i') }}
    </div>
    @endif
</div>

{{-- Replies --}}
@if($ticket->replies->isNotEmpty())
<div class="section">
    <div class="section-title" style="margin-bottom:1rem;">{{ __('portal.replies') }}</div>

    @foreach($ticket->replies as $reply)
    @php $isClient = ($reply->user_id === auth()->id()); @endphp
    <div class="reply-bubble {{ $isClient ? 'reply-client' : 'reply-office' }}">
        <div class="reply-meta">
            {{ $isClient ? __('portal.you') : ($reply->user?->name ?? __('portal.office_team')) }}
            · {{ $reply->created_at->format('Y/m/d H:i') }}
        </div>
        <div style="white-space:pre-wrap;">{{ $reply->content }}</div>
    </div>
    @endforeach
</div>
@endif

{{-- Reply form --}}
@if(!in_array($ticket->status, ['closed']))
<div class="card">
    <div style="font-size:.95rem;font-weight:700;color:#1E3A5F;margin-bottom:1rem;">{{ __('portal.add_reply') }}</div>
    <form method="POST" action="{{ route('portal.tickets.reply', $ticket->id) }}">
        @csrf
        <div class="form-group">
            <textarea name="content" class="form-control" rows="4"
                      placeholder="{{ __('portal.reply_placeholder') }}"
                      maxlength="3000" required>{{ old('content') }}</textarea>
            @error('content')
                <div style="color:#b91c1c;font-size:.8rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>
        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">{{ __('portal.send_reply') }}</button>
        </div>
    </form>
</div>
@else
<div class="alert alert-info">{{ __('portal.ticket_closed_notice') }}</div>
@endif

@endsection
