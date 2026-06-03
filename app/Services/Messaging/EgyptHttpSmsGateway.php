<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;

/**
 * Generic configurable HTTP SMS gateway — fits most Egyptian providers
 * (SMSMisr, Victory Link, Connekio…) that expose a simple GET/POST endpoint
 * with username/password/sender/mobile/message params.
 */
class EgyptHttpSmsGateway extends AbstractMessagingGateway implements SmsGatewayInterface
{
    public function send(string $to, string $body): array
    {
        $url = $this->config['url'] ?? '';

        if (! $url) {
            return $this->errorResponse('عنوان بوابة SMS غير مضبوط');
        }

        $payload = [
            'username' => $this->config['username'] ?? '',
            'password' => $this->config['password'] ?? '',
            'sender'   => $this->config['sender'] ?? '',
            'mobile'   => $this->normalize($to),
            'message'  => $body,
            'language' => $this->config['lang'] ?? '2', // 2 = unicode/Arabic in many EG gateways
        ];

        try {
            $method = strtolower($this->config['method'] ?? 'get');

            $response = $method === 'post'
                ? Http::asForm()->post($url, $payload)
                : Http::get($url, $payload);

            return $response->successful()
                ? $this->successResponse(['raw' => $response->body()])
                : $this->errorResponse('فشل الإرسال: ' . $response->status());
        } catch (\Throwable $e) {
            $this->log('error', 'EG SMS send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function test(): bool
    {
        // Most local gateways have no auth-check endpoint; consider configured
        // when the essential fields are present.
        return filled($this->config['url'] ?? null)
            && filled($this->config['username'] ?? null)
            && filled($this->config['sender'] ?? null);
    }

    private function normalize(string $number): string
    {
        return preg_replace('/[^\d]/', '', $number);
    }
}
