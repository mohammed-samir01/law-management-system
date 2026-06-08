@extends('portal.layouts.app')

@section('title', __('portal.my_invoices'))
@section('hero-title', __('portal.my_invoices'))
@section('hero-subtitle', __('portal.invoices_subtitle'))

@section('content')

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
                    <th>{{ __('portal.case_number') }}</th>
                    <th>{{ __('portal.amount') }}</th>
                    <th>{{ __('portal.due_date') }}</th>
                    <th>{{ __('portal.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td style="font-weight:600;">{{ $invoice->invoice_number }}</td>
                    <td style="color:#64748b;">{{ $invoice->legalCase?->case_number ?? '—' }}</td>
                    <td class="amount">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                    <td style="color:#64748b;">{{ $invoice->due_date?->format('Y/m/d') ?? '—' }}</td>
                    <td><span class="badge badge-{{ $invoice->status }}">{{ $invoice->status_label }}</span></td>
                    <td>
                        <a href="{{ route('portal.invoices.show', $invoice->id) }}" class="btn btn-primary btn-sm">
                            {{ __('portal.view') }}
                            @if(in_array($invoice->status, ['sent','overdue'])) / {{ __('portal.pay') }} @endif
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
