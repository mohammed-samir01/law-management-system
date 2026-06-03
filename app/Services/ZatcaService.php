<?php

namespace App\Services;

use App\Models\Invoice;

class ZatcaService
{
    /**
     * Build the ZATCA Phase-1 (e-invoice) QR payload: a base64-encoded TLV of
     * 5 tags (seller, VAT number, timestamp, total with VAT, VAT amount).
     * This is the exact string that goes inside the QR image.
     */
    public function qrPayload(Invoice $invoice): string
    {
        $office = $invoice->office;

        $fields = [
            1 => $office?->name ?? config('app.name'),
            2 => $office?->tax_number ?? '',
            3 => ($invoice->created_at ?? now())->toIso8601String(),
            4 => number_format((float) $invoice->total_amount, 2, '.', ''),
            5 => number_format((float) ($invoice->tax_amount ?? 0), 2, '.', ''),
        ];

        $tlv = '';
        foreach ($fields as $tag => $value) {
            $bytes = (string) $value;
            $tlv  .= chr($tag) . chr(strlen($bytes)) . $bytes;
        }

        return base64_encode($tlv);
    }
}
