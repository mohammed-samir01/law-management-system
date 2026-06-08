@extends('portal.layouts.app')

@section('title', __('portal.hearings'))
@section('hero-title', __('portal.hearings'))
@section('hero-subtitle', __('portal.hearings_subtitle'))

@section('content')

{{-- Upcoming --}}
<div class="section">
    <div class="section-header">
        <div class="section-title">{{ __('portal.upcoming_hearings') }}</div>
    </div>

    @if($upcoming->isEmpty())
        <div class="empty">
            <div class="empty-icon">📅</div>
            <p>{{ __('portal.no_hearings') }}</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('portal.date') }}</th>
                        <th>{{ __('portal.case_number') }}</th>
                        <th>{{ __('portal.court') }}</th>
                        <th>{{ __('portal.status') }}</th>
                        <th>{{ __('portal.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcoming as $hearing)
                    <tr>
                        <td>
                            <strong>{{ $hearing->scheduled_at->format('Y/m/d') }}</strong><br>
                            <small style="color:#94a3b8">{{ $hearing->scheduled_at->format('H:i') }}</small>
                        </td>
                        <td>
                            @if($hearing->legalCase)
                                <a href="{{ route('portal.cases.show', $hearing->legalCase->id) }}"
                                   style="color:#1E3A5F;text-decoration:none;font-weight:600;">
                                    {{ $hearing->legalCase->case_number }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $hearing->location ?? '—' }}</td>
                        <td><span class="badge badge-scheduled">{{ __('portal.hearing_scheduled') }}</span></td>
                        <td style="font-size:.83rem;color:#64748b;">{{ Str::limit($hearing->notes ?? '—', 50) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Past --}}
@if($past->isNotEmpty())
<div class="section">
    <div class="section-header">
        <div class="section-title">{{ __('portal.past_hearings') }}</div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>{{ __('portal.date') }}</th>
                    <th>{{ __('portal.case_number') }}</th>
                    <th>{{ __('portal.court') }}</th>
                    <th>{{ __('portal.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($past as $hearing)
                <tr style="color:#64748b;">
                    <td>
                        {{ $hearing->scheduled_at->format('Y/m/d') }}<br>
                        <small>{{ $hearing->scheduled_at->format('H:i') }}</small>
                    </td>
                    <td>
                        @if($hearing->legalCase)
                            <a href="{{ route('portal.cases.show', $hearing->legalCase->id) }}"
                               style="color:#475569;text-decoration:none;">
                                {{ $hearing->legalCase->case_number }}
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $hearing->location ?? '—' }}</td>
                    <td><span class="badge badge-{{ $hearing->status }}">{{ $hearing->status_label }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
