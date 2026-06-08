@extends('portal.layouts.app')

@section('title', __('portal.nav_dashboard'))
@section('hero-title', __('portal.welcome', ['name' => auth()->user()->name]))
@section('hero-subtitle', now()->translatedFormat('l، d F Y'))

@section('content')

{{-- Stats bar --}}
<div style="display:flex;flex-wrap:wrap;gap:1rem 2.5rem;background:#fff;border-radius:12px;padding:.85rem 1.5rem;margin-bottom:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,.05);border:1px solid #f1f5f9;">
    <div style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;color:#64748b;">
        <span style="font-size:1.25rem;font-weight:700;color:#1E3A5F;">{{ $cases->count() }}</span>
        {{ __('portal.my_cases') }}
    </div>
    <div style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;color:#64748b;">
        <span style="font-size:1.25rem;font-weight:700;color:#1E3A5F;">{{ $upcomingHearings->count() }}</span>
        {{ __('portal.upcoming_hearings') }}
    </div>
    <div style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;color:#64748b;">
        <span style="font-size:1.25rem;font-weight:700;color:#1E3A5F;">{{ $invoices->count() }}</span>
        {{ __('portal.my_invoices') }}
    </div>
</div>

{{-- My Cases --}}
<div class="section">
    <div class="section-header">
        <div class="section-title">{{ __('portal.my_cases') }}</div>
        <a href="{{ route('portal.cases.index') }}" class="btn btn-outline btn-sm">{{ __('portal.view_all') }}</a>
    </div>

    @if($cases->isEmpty())
        <div class="empty">
            <div class="empty-icon">⚖️</div>
            <p>{{ __('portal.no_cases') }}</p>
        </div>
    @else
        <div class="grid">
            @foreach($cases->take(6) as $case)
            <a href="{{ route('portal.cases.show', $case->id) }}" style="text-decoration:none;color:inherit;">
                <div class="card">
                    <div class="card-meta">
                        <span class="card-number">{{ $case->case_number }}</span>
                        <span class="badge badge-{{ $case->status }}">{{ $case->status_label }}</span>
                    </div>
                    <div class="card-title">{{ $case->getTranslation('title', app()->getLocale()) ?: $case->getTranslation('title', 'ar') }}</div>
                    <div class="card-sub">🏛️ {{ $case->court }}</div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Upcoming Hearings --}}
<div class="section">
    <div class="section-header">
        <div class="section-title">{{ __('portal.upcoming_hearings') }}</div>
        <a href="{{ route('portal.hearings.index') }}" class="btn btn-outline btn-sm">{{ __('portal.view_all') }}</a>
    </div>

    @if($upcomingHearings->isEmpty())
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
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingHearings as $hearing)
                    <tr>
                        <td>
                            <strong>{{ $hearing->scheduled_at->format('Y/m/d') }}</strong><br>
                            <small style="color:#94a3b8">{{ $hearing->scheduled_at->format('H:i') }}</small>
                        </td>
                        <td>{{ $hearing->legalCase?->case_number ?? '—' }}</td>
                        <td>{{ $hearing->location ?? '—' }}</td>
                        <td><span class="badge badge-scheduled">{{ __('portal.hearing_scheduled') }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Invoices --}}
<div class="section">
    <div class="section-header">
        <div class="section-title">{{ __('portal.my_invoices') }}</div>
        <a href="{{ route('portal.invoices.index') }}" class="btn btn-outline btn-sm">{{ __('portal.view_all') }}</a>
    </div>

    @if($invoices->isEmpty())
        <div class="empty">
            <div class="empty-icon">🧾</div>
            <p>{{ __('portal.no_invoices') }}</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('portal.invoice_number') }}</th>
                        <th>{{ __('portal.amount') }}</th>
                        <th>{{ __('portal.due_date') }}</th>
                        <th>{{ __('portal.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices->take(5) as $invoice)
                    <tr>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                        <td class="amount">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                        <td>{{ $invoice->due_date?->format('Y/m/d') ?? '—' }}</td>
                        <td><span class="badge badge-{{ $invoice->status }}">{{ __('invoices.statuses.' . $invoice->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
