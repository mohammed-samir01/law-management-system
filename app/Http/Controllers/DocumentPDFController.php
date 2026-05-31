<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\PDFService;

class DocumentPDFController extends Controller
{
    public function download(Document $document, PDFService $pdf)
    {
        return $pdf->downloadDocument($document);
    }
}
