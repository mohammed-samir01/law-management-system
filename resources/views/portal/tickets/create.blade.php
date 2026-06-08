@extends('portal.layouts.app')

@section('title', __('portal.new_ticket'))
@section('hero-title', __('portal.new_ticket'))
@section('hero-subtitle', __('portal.new_ticket_subtitle'))

@push('styles')
<style>
    .container { max-width: 680px; }
    .back-link { display:inline-flex;align-items:center;gap:.4rem;color:#64748b;text-decoration:none;font-size:.88rem;margin-bottom:1.25rem; }
    .back-link:hover { color:#1E3A5F; }
    select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat:no-repeat;background-position:left .75rem center;padding-left:2.5rem; }
    [dir=rtl] select.form-control { background-position:right .75rem center;padding-left:.85rem;padding-right:2.5rem; }
    .char-count { font-size:.76rem;color:#94a3b8;text-align:left;margin-top:.25rem; }
</style>
@endpush

@section('content')

<a href="{{ route('portal.tickets.index') }}" class="back-link">← {{ __('portal.back_to_tickets') }}</a>

<div class="card">
    <div style="font-size:1rem;font-weight:700;color:#1E3A5F;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:1px solid #f1f5f9;">
        {{ __('portal.new_ticket') }}
    </div>

    <form method="POST" action="{{ route('portal.tickets.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="title">{{ __('portal.ticket_title') }} *</label>
            <input type="text" id="title" name="title" class="form-control"
                   value="{{ old('title') }}"
                   placeholder="{{ __('portal.ticket_title_placeholder') }}"
                   maxlength="255" required>
            @error('title')
                <div style="color:#b91c1c;font-size:.8rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="priority">{{ __('portal.priority') }} *</label>
            <select id="priority" name="priority" class="form-control" required>
                <option value="normal"  {{ old('priority','normal') === 'normal'  ? 'selected' : '' }}>{{ __('portal.priority_normal') }}</option>
                <option value="urgent"  {{ old('priority') === 'urgent'  ? 'selected' : '' }}>{{ __('portal.priority_urgent') }}</option>
                <option value="low"     {{ old('priority') === 'low'     ? 'selected' : '' }}>{{ __('portal.priority_low') }}</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="description">{{ __('portal.ticket_description') }} *</label>
            <textarea id="description" name="description" class="form-control"
                      rows="6"
                      placeholder="{{ __('portal.ticket_description_placeholder') }}"
                      maxlength="3000"
                      oninput="document.getElementById('charCount').textContent = this.value.length + '/3000'"
                      required>{{ old('description') }}</textarea>
            <div class="char-count" id="charCount">0/3000</div>
            @error('description')
                <div style="color:#b91c1c;font-size:.8rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:1.5rem;">
            <a href="{{ route('portal.tickets.index') }}" class="btn btn-outline">{{ __('portal.cancel') }}</a>
            <button type="submit" class="btn btn-gold">{{ __('portal.submit_ticket') }}</button>
        </div>
    </form>
</div>

@endsection
