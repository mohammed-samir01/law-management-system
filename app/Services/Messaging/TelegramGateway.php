<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Http;

class TelegramGateway extends AbstractMessagingGateway
{
    private function base(): string
    {
        return 'https://api.telegram.org/bot' . ($this->config['bot_token'] ?? '');
    }

    public function send(string $chatId, string $body): array
    {
        if (blank($this->config['bot_token'] ?? null) || blank($chatId)) {
            return $this->errorResponse('بيانات Telegram غير مكتملة');
        }

        try {
            $response = Http::post($this->base() . '/sendMessage', [
                'chat_id' => $chatId,
                'text'    => $body,
            ]);

            return $response->successful()
                ? $this->successResponse(['message_id' => $response->json('result.message_id')])
                : $this->errorResponse($response->json('description', 'فشل الإرسال'));
        } catch (\Throwable $e) {
            $this->log('error', 'Telegram send failed', ['chat' => $chatId, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function test(): bool
    {
        if (blank($this->config['bot_token'] ?? null)) {
            return false;
        }

        return Http::get($this->base() . '/getMe')->successful();
    }

    /**
     * Register the webhook URL with Telegram (one-time setup).
     */
    public function setWebhook(string $url): array
    {
        try {
            $response = Http::post($this->base() . '/setWebhook', ['url' => $url]);

            return $response->successful() && $response->json('ok')
                ? $this->successResponse(['result' => $response->json('description')])
                : $this->errorResponse($response->json('description', 'فشل تسجيل الويبهوك'));
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
