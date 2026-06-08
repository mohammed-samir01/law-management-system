@extends('portal.layouts.app')

@section('title', $case->case_number)
@section('hero-title', $case->getTranslation('title', app()->getLocale()) ?: $case->getTranslation('title', 'ar'))
@section('hero-subtitle', '{{ __("portal.case_number_label") }}: ' . $case->case_number)

@push('styles')
<style>
    .container { max-width: 900px; }
    .back-link { display:inline-flex;align-items:center;gap:.4rem;color:#64748b;text-decoration:none;font-size:.88rem;margin-bottom:1.25rem; }
    .back-link:hover { color:#1E3A5F; }
    .detail-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem 1.5rem;margin-bottom:1rem; }
    .detail-item label { font-size:.78rem;color:#94a3b8;display:block;margin-bottom:.2rem; }
    .detail-item span { font-size:.9rem;font-weight:600;color:#1e293b; }
    .doc-row { display:flex;align-items:center;justify-content:space-between;padding:.65rem 0;border-bottom:1px solid #f1f5f9; }
    .doc-row:last-child { border-bottom:none; }
    .doc-name { font-size:.88rem;color:#1e293b;font-weight:500; }
    .doc-meta { font-size:.76rem;color:#94a3b8; }
</style>
@endpush

@section('content')

<a href="{{ route('portal.cases.index') }}" class="back-link">← {{ __('portal.back_to_cases') }}</a>

{{-- Case Info --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:1px solid #f1f5f9;">
        <span style="font-size:1.1rem;font-weight:700;color:#1E3A5F;">{{ $case->case_number }}</span>
        <span class="badge badge-{{ $case->status }}">{{ $case->status_label }}</span>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <label>{{ __('portal.case_type') }}</label>
            <span>{{ $case->case_type ?? '—' }}</span>
        </div>
        <div class="detail-item">
            <label>{{ __('portal.court') }}</label>
            <span>{{ $case->court ?? '—' }}</span>
        </div>
        <div class="detail-item">
            <label>{{ __('portal.judge') }}</label>
            <span>{{ $case->judge ?? '—' }}</span>
        </div>
        <div class="detail-item">
            <label>{{ __('portal.opened_on', ['date' => '']) }}</label>
            <span>{{ $case->created_at->format('Y/m/d') }}</span>
        </div>
        @if($case->closed_at)
        <div class="detail-item">
            <label>{{ __('portal.closed_on') }}</label>
            <span>{{ $case->closed_at->format('Y/m/d') }}</span>
        </div>
        @endif
    </div>

    @if($case->getTranslation('description', app()->getLocale()) ?: $case->getTranslation('description', 'ar'))
    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9;font-size:.9rem;color:#475569;line-height:1.7;">
        {{ $case->getTranslation('description', app()->getLocale()) ?: $case->getTranslation('description', 'ar') }}
    </div>
    @endif
</div>

{{-- Hearings --}}
<div class="section">
    <div class="section-header">
        <div class="section-title">{{ __('portal.hearings') }}</div>
    </div>

    @if($hearings->isEmpty())
        <div class="empty">
            <div class="empty-icon">📅</div>
            <p>{{ __('portal.no_hearings_for_case') }}</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('portal.date') }}</th>
                        <th>{{ __('portal.court') }}</th>
                        <th>{{ __('portal.status') }}</th>
                        <th>{{ __('portal.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hearings as $hearing)
                    <tr>
                        <td>
                            <strong>{{ $hearing->scheduled_at->format('Y/m/d') }}</strong><br>
                            <small style="color:#94a3b8">{{ $hearing->scheduled_at->format('H:i') }}</small>
                        </td>
                        <td>{{ $hearing->location ?? '—' }}</td>
                        <td><span class="badge badge-{{ $hearing->status }}">{{ $hearing->status_label }}</span></td>
                        <td style="font-size:.83rem;color:#64748b;">{{ Str::limit($hearing->notes ?? '—', 60) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Documents --}}
<div class="section">
    <div class="section-header">
        <div class="section-title">{{ __('portal.documents') }}</div>
    </div>

    @if($documents->isEmpty())
        <div class="empty">
            <div class="empty-icon">📄</div>
            <p>{{ __('portal.no_documents_for_case') }}</p>
        </div>
    @else
        <div class="card">
            @foreach($documents as $doc)
            @php $media = $doc->getFirstMedia('files'); @endphp
            <div class="doc-row">
                <div>
                    <div class="doc-name">
                        {{ $doc->getTranslation('title', app()->getLocale()) ?: $doc->getTranslation('title', 'ar') }}
                    </div>
                    <div class="doc-meta">
                        {{ $doc->created_at->format('Y/m/d') }}
                        @if($media) · {{ strtoupper($media->extension) }} · {{ round($media->size / 1024) }} KB @endif
                    </div>
                </div>
                @if($media)
                <a href="{{ route('portal.documents.download', $doc->id) }}" class="btn btn-outline btn-sm">
                    ⬇ {{ __('portal.download') }}
                </a>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
