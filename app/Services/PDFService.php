<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Invoice;
use App\Models\LegalCase;
use App\Support\Pdf;
use Illuminate\Support\Facades\Storage;

class PDFService
{
    public function generateInvoicePDF(Invoice $invoice): string
    {
        $invoice->loadMissing(['client', 'legalCase', 'office']);

        $path = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('public')->put($path, Pdf::make('pdf.invoice', compact('invoice')));

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    public function generateCaseReportPDF(LegalCase $case): string
    {
        $case->loadMissing(['client', 'lawyers', 'hearings', 'documents', 'office', 'aiResults']);

        $path = "cases/{$case->case_number}-report.pdf";
        Storage::disk('public')->put($path, Pdf::make('pdf.case-report', compact('case')));

        return $path;
    }

    public function downloadDocument(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $document->loadMissing(['office', 'uploadedBy', 'documentable']);

        $bytes    = Pdf::make('pdf.document', compact('document'));
        $filename = 'document-' . $document->id . '.pdf';

        return response()->streamDownload(
            fn () => print($bytes),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function download(Invoice $invoice): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (! $invoice->pdf_path || ! Storage::disk('public')->exists($invoice->pdf_path)) {
            $this->generateInvoicePDF($invoice);
        }

        return Storage::disk('public')->download(
            $invoice->pdf_path,
            "invoice-{$invoice->invoice_number}.pdf"
        );
    }
}
