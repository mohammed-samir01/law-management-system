<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        if (! $client) {
            return view('portal.documents.index', ['documents' => collect()]);
        }

        $caseIds = LegalCase::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->pluck('id');

        $documents = Document::withoutGlobalScopes()
            ->where('documentable_type', LegalCase::class)
            ->whereIn('documentable_id', $caseIds)
            ->with('documentable')
            ->latest()
            ->get();

        return view('portal.documents.index', compact('documents'));
    }

    public function download(Request $request, int $id): StreamedResponse|Response
    {
        $client = $request->user()->client;

        $caseIds = LegalCase::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->pluck('id');

        $document = Document::withoutGlobalScopes()
            ->where('documentable_type', LegalCase::class)
            ->whereIn('documentable_id', $caseIds)
            ->findOrFail($id);

        $media = $document->getFirstMedia('files');

        abort_if(! $media, 404);

        return response()->streamDownload(function () use ($media) {
            echo file_get_contents($media->getPath());
        }, $media->file_name, [
            'Content-Type' => $media->mime_type,
        ]);
    }
}
