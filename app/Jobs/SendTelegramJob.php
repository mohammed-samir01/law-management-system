<?php

namespace App\Jobs;

use App\Services\Messaging\MessagingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public string $chatId,
        public string $body,
    ) {}

    public function handle(): void
    {
        $gateway = MessagingService::resolveTelegram();

        if (! $gateway) {
            Log::warning('SendTelegramJob: Telegram not configured');
            return;
        }

        if (blank($this->chatId)) {
            Log::warning('SendTelegramJob: empty chat id');
            return;
        }

        $result = $gateway->send($this->chatId, $this->body);

        if (! ($result['success'] ?? false)) {
            throw new \RuntimeException($result['message'] ?? 'Telegram send failed');
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendTelegramJob failed', ['chat' => $this->chatId, 'error' => $e->getMessage()]);
    }
}
