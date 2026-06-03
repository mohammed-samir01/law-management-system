<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;

class TwilioSmsGateway extends AbstractMessagingGateway implements SmsGatewayInterface
{
    private string $baseUrl = 'https://api.twilio.com/2010-04-01';

    public function send(string $to, string $body): array
    {
        $sid  = $this->config['sid'] ?? '';
        $from = $this->config['sms_from'] ?? '';

        if (! $sid || ! $from) {
            return $this->errorResponse('بيانات اعتماد Twilio غير مكتملة');
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($sid, $this->config['token'] ?? '')
                ->post("{$this->baseUrl}/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To'   => $to,
                    'Body' => $body,
                ]);

            return $response->successful()
                ? $this->successResponse(['message_id' => $response->json('sid')])
                : $this->errorResponse($response->json('message', 'فشل الإرسال'));
        } catch (\Throwable $e) {
            $this->log('error', 'Send SMS failed', ['to' => $to, 'error' => $e->getMessage()]);
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
}
