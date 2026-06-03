<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;

class VonageSmsGateway extends AbstractMessagingGateway implements SmsGatewayInterface
{
    public function send(string $to, string $body): array
    {
        $key    = $this->config['key'] ?? '';
        $secret = $this->config['secret'] ?? '';
        $from   = $this->config['from'] ?? '';

        if (! $key || ! $secret || ! $from) {
            return $this->errorResponse('بيانات اعتماد Vonage غير مكتملة');
        }

        try {
            $response = Http::asForm()->post('https://rest.nexmo.com/sms/json', [
                'api_key'    => $key,
                'api_secret' => $secret,
                'from'       => $from,
                'to'         => preg_replace('/[^\d]/', '', $to),
                'text'       => $body,
                'type'       => 'unicode',
            ]);

            $status = $response->json('messages.0.status');

            return ($response->successful() && $status === '0')
                ? $this->successResponse(['message_id' => $response->json('messages.0.message-id')])
                : $this->errorResponse($response->json('messages.0.error-text', 'فشل الإرسال'));
        } catch (\Throwable $e) {
            $this->log('error', 'Vonage send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function test(): bool
    {
        $key    = $this->config['key'] ?? '';
        $secret = $this->config['secret'] ?? '';

        if (! $key || ! $secret) {
            return false;
        }

        return Http::get('https://rest.nexmo.com/account/get-balance', [
            'api_key'    => $key,
            'api_secret' => $secret,
        ])->successful();
    }
}
