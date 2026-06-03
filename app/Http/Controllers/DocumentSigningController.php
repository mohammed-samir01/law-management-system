<?php

namespace App\Http\Controllers;

use App\Services\DocumentSigningService;
use Illuminate\Http\Request;

class DocumentSigningController extends Controller
{
    public function __construct(private readonly DocumentSigningService $service) {}

    /**
     * Public signing page — no auth, gated by the signed token only.
     */
    public function show(string $token)
    {
        $document = $this->service->resolveByToken($token);

        if (! $document) {
            abort(404, __('addons.esign_invalid_link'));
        }

        if ($document->signing_status === 'signed') {
            return view('sign.done', ['document' => $document, 'already' => true]);
        }

        return view('sign.show', ['document' => $document, 'token' => $token]);
    }

    public function store(Request $request, string $token)
    {
        $document = $this->service->resolveByToken($token);

        if (! $document || $document->signing_status !== 'pending') {
            abort(404, __('addons.esign_invalid_link'));
        }

        $data = $request->validate([
            'signature' => ['required', 'string', 'starts_with:data:image/png;base64,'],
        ]);

        $this->service->sign($document, $data['signature'], $request->ip());

        return redirect()->route('documents.sign.show', $token);
    }
}
