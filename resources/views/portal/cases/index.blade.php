@extends('portal.layouts.app')

@section('title', __('portal.my_cases'))
@section('hero-title', __('portal.my_cases'))
@section('hero-subtitle', __('portal.cases_subtitle', ['count' => $cases->count()]))

@section('content')

@if($cases->isEmpty())
    <div class="empty">
        <div class="empty-icon">⚖️</div>
        <p>{{ __('portal.no_cases') }}</p>
    </div>
@else
    <div class="grid">
        @foreach($cases as $case)
        <a href="{{ route('portal.cases.show', $case->id) }}" style="text-decoration:none;color:inherit;display:block;">
            <div class="card" style="height:100%;">
                <div class="card-meta">
                    <span class="card-number">{{ $case->case_number }}</span>
                    <span class="badge badge-{{ $case->status }}">{{ $case->status_label }}</span>
                </div>
                <div class="card-title">
                    {{ $case->getTranslation('title', app()->getLocale()) ?: $case->getTranslation('title', 'ar') }}
                </div>
                @if($case->court)
                    <div class="card-sub" style="margin-bottom:.35rem;">🏛️ {{ $case->court }}</div>
                @endif
                @if($case->case_type)
                    <div class="card-sub">🗂️ {{ $case->case_type }}</div>
                @endif
                <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid #f1f5f9;font-size:.78rem;color:#94a3b8;">
                    {{ __('portal.opened_on', ['date' => $case->created_at->format('Y/m/d')]) }}
                </div>
            </div>
        </a>
        @endforeach
    </div>
@endif

@endsection
