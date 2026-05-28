<?php

namespace App\Notifications\Channels;

use App\Models\DeviceToken;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class NativePushChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toPush')) {
            return;
        }

        $credentialsPath = storage_path('app/firebase-credentials.json');

        if (! file_exists($credentialsPath)) {
            Log::warning('Firebase credentials not found — push notification skipped');
            return;
        }

        $tokens = DeviceToken::where('user_id', $notifiable->id)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $payload = $notification->toPush($notifiable);

        try {
            $firebase = (new Factory)->withServiceAccount($credentialsPath);
            $messaging = $firebase->createMessaging();

            foreach ($tokens as $token) {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(FcmNotification::create(
                        $payload['title'] ?? 'عامر',
                        $payload['body'] ?? ''
                    ))
                    ->withData($payload['data'] ?? []);

                $messaging->send($message);
            }
        } catch (\Throwable $e) {
            Log::error('FCM push notification failed', [
                'error' => $e->getMessage(),
                'user_id' => $notifiable->id,
            ]);
        }
    }
}
