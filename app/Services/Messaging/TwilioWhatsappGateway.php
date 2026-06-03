<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Contracts\WhatsappGatewayInterface;
use Illuminate\Support\Facades\Http;

class TwilioWhatsappGateway extends AbstractMessagingGateway implements WhatsappGatewayInterface
{
    private string $baseUrl = 'https://api.twilio.com/2010-04-01';

    public function send(string $to, string $body): array
    {
        $sid  = $this->config['sid'] ?? '';
        $from = $this->config['whatsapp_from'] ?? '';

        if (! $sid || ! $from) {
            return $this->errorResponse('بيانات اعتماد Twilio غير مكتملة');
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($sid, $this->config['token'] ?? '')
                ->post("{$this->baseUrl}/Accounts/{$sid}/Messages.json", [
                    'From' => $this->prefix($from),
                    'To'   => $this->prefix($to),
                    'Body' => $body,
                ]);

            return $response->successful()
                ? $this->successResponse(['message_id' => $response->json('sid')])
                : $this->errorResponse($response->json('message', 'فشل الإرسال'));
        } catch (\Throwable $e) {
            $this->log('error', 'Send WhatsApp failed', ['to' => $to, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function test(): bool
    {
        $sid = $this->config['sid'] ?? '';

        if (! $sid) {
            return false;
        }

        return Http::withBasicAuth($sid, $this->config['token'] ?? '')
            ->get("{$this->baseUrl}/Accounts/{$sid}.json")
            ->successful();
    }

    private function prefix(string $number): string
    {
        return str_starts_with($number, 'whatsapp:') ? $number : 'whatsapp:'.$number;
    }
}
