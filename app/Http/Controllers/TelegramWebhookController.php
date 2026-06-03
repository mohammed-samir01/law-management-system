<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PlatformSetting;
use App\Services\Messaging\MessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Public Telegram webhook (no auth/CSRF). Validates the secret in the URL,
     * then links an incoming "/start <token>" to the matching client by token.
     */
    public function handle(Request $request, string $secret)
    {
        $config = PlatformSetting::messaging();
        $expected = $config['telegram']['webhook_secret'] ?? null;

        if (! $expected || ! hash_equals($expected, $secret)) {
            abort(403);
        }

        $message = $request->input('message', []);
        $text    = $message['text'] ?? '';
        $chatId  = $message['chat']['id'] ?? null;

        if (! $chatId || ! str_starts_with($text, '/start')) {
            return response()->json(['ok' => true]);
        }

        $token = trim(substr($text, strlen('/start')));

        if ($token === '') {
            return response()->json(['ok' => true]);
        }

        $client = Client::withoutGlobalScopes()
            ->where('telegram_link_token', $token)
            ->first();

        if ($client) {
            $client->forceFill([
                'telegram_chat_id'    => (string) $chatId,
                'telegram_link_token' => null,
            ])->save();

            $gateway = MessagingService::resolveTelegram();
            $gateway?->send((string) $chatId, '✅ تم ربط حسابك بنجاح. ستصلك تذكيرات الجلسات هنا.');
        } else {
            Log::info('Telegram webhook: no client for token', ['token' => $token]);
        }

        return response()->json(['ok' => true]);
    }
}
