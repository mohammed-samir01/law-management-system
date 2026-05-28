<?php

namespace App\Http\Controllers\Mobile\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\LegalCase;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentViewController extends Controller
{
    public function show(int $caseId, int $docId): BinaryFileResponse|Response
    {
        $case = LegalCase::findOrFail($caseId);

        $document = Document::where('legal_case_id', $case->id)
            ->findOrFail($docId);

        $media = $document->getFirstMedia('files');

        if (! $media) {
            abort(404);
        }

        return response()->file($media->getPath(), [
            'Content-Type'        => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
        ]);
    }
}
