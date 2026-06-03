<?php

namespace App\Services;

use App\Jobs\SendSmsJob;
use App\Jobs\SendWhatsappJob;
use App\Models\Client;
use App\Models\Document;
use App\Models\Office;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentSigningService
{
    /**
     * Create a signing request: generate a token, mark the document pending,
     * and notify the client with the signing link.
     */
    public function requestSignature(Document $document, Client $client): void
    {
        $document->forceFill([
            'signing_status'     => 'pending',
            'signing_token'      => Str::random(48),
            'signing_expires_at' => now()->addDays(14),
            'signing_client_id'  => $client->id,
            'signed_at'          => null,
            'signer_ip'          => null,
            'signature_data'     => null,
            'signed_pdf_path'    => null,
        ])->save();

        $this->notifyClient($document, $client);
    }

    /**
     * Notify the client of a pending signature via email + (if the office has the
     * relevant addon) SMS / WhatsApp, with the public signing link.
     */
    private function notifyClient(Document $document, Client $client): void
    {
        $url   = route('documents.sign.show', $document->signing_token);
        $title = $document->getTranslation('title', 'ar') ?: $document->getTranslation('title', 'en') ?: '';
        $body  = __('addons.esign_notify_body') . "\n" . $url;

        // Email (best effort — mail may not be configured yet).
        if (filled($client->email)) {
            try {
                Mail::raw($body, function ($m) use ($client) {
                    $m->to($client->email)->subject(__('addons.esign_notify_subject'));
                });
            } catch (\Throwable $e) {
                Log::warning('DocumentSigning: email send failed', ['error' => $e->getMessage()]);
            }
        }

        if (blank($client->phone)) {
            return;
        }

        $office = Office::withoutGlobalScopes()->find($document->office_id);
        if (! $office) {
            return;
        }

        if ($office->hasAddon('sms')) {
            SendSmsJob::dispatch($client->phone, __('addons.esign_notify_subject') . ' — ' . $url);
        }

        if ($office->hasAddon('whatsapp')) {
            SendWhatsappJob::dispatch($client->phone, "✍️ {$title}\n" . $body);
        }
    }

    /**
     * Resolve a pending document by its signing token (and validate expiry).
     */
    public function resolveByToken(string $token): ?Document
    {
        $document = Document::withoutGlobalScopes()
            ->where('signing_token', $token)
            ->first();

        if (! $document) {
            return null;
        }

        if ($document->signing_status !== 'pending') {
            return $document; // caller decides (already signed / rejected)
        }

        if ($document->signing_expires_at && $document->signing_expires_at->isPast()) {
            return null;
        }

        return $document;
    }

    /**
     * Apply a captured signature (base64 PNG data URL), stamp a certificate PDF,
     * and finalize the document as signed.
     */
    public function sign(Document $document, string $signatureDataUrl, string $ip): void
    {
        $document->forceFill([
            'signing_status'  => 'signed',
            'signed_at'       => now(),
            'signer_ip'       => $ip,
            'signature_data'  => $signatureDataUrl,
        ])->save();

        $document->forceFill([
            'signed_pdf_path' => $this->generateCertificate($document),
        ])->save();
    }

    /**
     * Render a signing certificate PDF embedding the signature image + metadata.
     */
    private function generateCertificate(Document $document): string
    {
        $document->loadMissing(['office', 'signingClient', 'documentable']);

        $path = "signed-documents/document-{$document->id}.pdf";
        Storage::disk('public')->put($path, \App\Support\Pdf::make('pdf.signed-document', ['document' => $document]));

        return $path;
    }
}
