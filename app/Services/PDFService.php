<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Invoice;
use App\Models\LegalCase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PDFService
{
    public function generateInvoicePDF(Invoice $invoice): string
    {
        $invoice->loadMissing(['client', 'legalCase', 'office']);

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
            ->setPaper('a4')
            ->setOption('defaultFont', 'Amiri')
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $path = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    public function generateCaseReportPDF(LegalCase $case): string
    {
        $case->loadMissing(['client', 'lawyers', 'hearings', 'documents', 'office']);

        $pdf = Pdf::loadView('pdf.case-report', compact('case'))
            ->setPaper('a4')
            ->setOption('defaultFont', 'Amiri')
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $path = "cases/{$case->case_number}-report.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function downloadDocument(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $document->loadMissing(['office', 'uploadedBy', 'documentable']);

        $pdf = Pdf::loadView('pdf.document', compact('document'))
            ->setPaper('a4')
            ->setOption('defaultFont', 'Amiri')
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('isRemoteEnabled', false);

        $filename = 'document-' . $document->id . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
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
