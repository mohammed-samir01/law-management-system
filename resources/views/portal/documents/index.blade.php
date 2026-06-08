@extends('portal.layouts.app')

@section('title', __('portal.my_documents'))
@section('hero-title', __('portal.my_documents'))
@section('hero-subtitle', __('portal.documents_subtitle'))

@section('content')

@if($documents->isEmpty())
    <div class="empty">
        <div class="empty-icon">📄</div>
        <p>{{ __('portal.no_documents') }}</p>
    </div>
@else
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>{{ __('portal.document_name') }}</th>
                    <th>{{ __('portal.case_number') }}</th>
                    <th>{{ __('portal.file_type') }}</th>
                    <th>{{ __('portal.date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                @php
                    $media    = $doc->getFirstMedia('files');
                    $docTitle = $doc->getTranslation('title', app()->getLocale()) ?: $doc->getTranslation('title', 'ar');
                    $case     = $doc->documentable;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $docTitle ?: ($media?->file_name ?? '—') }}</div>
                        @if($media)
                            <div style="font-size:.76rem;color:#94a3b8;">{{ round($media->size / 1024) }} KB</div>
                        @endif
                    </td>
                    <td>
                        @if($case)
                            <a href="{{ route('portal.cases.show', $case->id) }}"
                               style="color:#1E3A5F;text-decoration:none;font-weight:500;">
                                {{ $case->case_number }}
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($media)
                            <span class="badge badge-draft">{{ strtoupper($media->extension) }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td style="color:#64748b;">{{ $doc->created_at->format('Y/m/d') }}</td>
                    <td>
                        @if($media)
                            <a href="{{ route('portal.documents.download', $doc->id) }}"
                               class="btn btn-outline btn-sm">
                                ⬇ {{ __('portal.download') }}
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
