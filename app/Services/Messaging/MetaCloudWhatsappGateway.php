<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Contracts\WhatsappGatewayInterface;
use Illuminate\Support\Facades\Http;

class MetaCloudWhatsappGateway extends AbstractMessagingGateway implements WhatsappGatewayInterface
{
    private string $base = 'https://graph.facebook.com/v21.0';

    /**
     * NOTE: Proactive (out-of-session) messages require an approved Meta template.
     * Free-form text only delivers within the 24h customer-service window.
     */
    public function send(string $to, string $body): array
    {
        $token   = $this->config['token'] ?? '';
        $phoneId = $this->config['phone_id'] ?? '';

        if (! $token || ! $phoneId) {
            return $this->errorResponse('بيانات اعتماد Meta غير مكتملة');
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->base}/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $this->normalize($to),
                    'type'              => 'text',
                    'text'              => ['body' => $body],
                ]);

            return $response->successful()
                ? $this->successResponse(['message_id' => $response->json('messages.0.id')])
                : $this->errorResponse($response->json('error.message', 'فشل الإرسال'));
        } catch (\Throwable $e) {
            $this->log('error', 'Meta send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function test(): bool
    {
        $token   = $this->config['token'] ?? '';
        $phoneId = $this->config['phone_id'] ?? '';

        if (! $token || ! $phoneId) {
            return false;
        }

        return Http::withToken($token)->get("{$this->base}/{$phoneId}")->successful();
    }

    private function normalize(string $number): string
    {
        // Meta expects digits only (country code + number, no leading + or spaces).
        return preg_replace('/[^\d]/', '', $number);
    }
}
