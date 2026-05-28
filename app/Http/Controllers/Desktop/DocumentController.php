<?php

namespace App\Http\Controllers\Desktop;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::with(['legalCase', 'office'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereRaw("JSON_EXTRACT(title, '$.ar') LIKE ?", ["%{$search}%"]);
        }

        $documents = $query->paginate(15)->withQueryString();

        return view('desktop.documents.index', compact('documents'));
    }

    public function view(int $id): BinaryFileResponse|Response
    {
        $document = Document::findOrFail($id);
        $media    = $document->getFirstMedia('files');

        if (! $media) {
            abort(404);
        }

        return response()->file($media->getPath(), [
            'Content-Type'        => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
        ]);
    }
}
