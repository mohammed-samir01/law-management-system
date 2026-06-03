<?php

namespace App\Jobs;

use App\Services\Messaging\MessagingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public string $to,
        public string $body,
    ) {}

    public function handle(): void
    {
        $gateway = MessagingService::resolveWhatsapp();

        if (! $gateway) {
            Log::warning('SendWhatsappJob: WhatsApp gateway not configured');
            return;
        }

        if (blank($this->to)) {
            Log::warning('SendWhatsappJob: empty recipient');
            return;
        }

        $result = $gateway->send($this->to, $this->body);

        if (! ($result['success'] ?? false)) {
            throw new \RuntimeException($result['message'] ?? 'WhatsApp send failed');
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendWhatsappJob failed', ['to' => $this->to, 'error' => $e->getMessage()]);
    }
}
