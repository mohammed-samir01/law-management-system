<?php

namespace App\Services\Messaging;

use App\Models\PlatformSetting;
use App\Services\Messaging\Contracts\SmsGatewayInterface;
use App\Services\Messaging\Contracts\WhatsappGatewayInterface;

class MessagingService
{
    /**
     * Resolve the configured SMS gateway (per-channel provider), or null.
     */
    public static function resolveSms(): ?SmsGatewayInterface
    {
        $c        = PlatformSetting::messaging();
        $provider = $c['sms_provider'] ?? 'twilio';

        return match ($provider) {
            'twilio'     => static::ifFilled(new TwilioSmsGateway($c['twilio'] ?? []), ['sid', 'token', 'sms_from'], $c['twilio'] ?? []),
            'egypt_http' => static::ifFilled(new EgyptHttpSmsGateway($c['egypt_http'] ?? []), ['url', 'username'], $c['egypt_http'] ?? []),
            'vonage'     => static::ifFilled(new VonageSmsGateway($c['vonage'] ?? []), ['key', 'secret', 'from'], $c['vonage'] ?? []),
            default      => null,
        };
    }

    /**
     * Resolve the configured WhatsApp gateway (per-channel provider), or null.
     */
    public static function resolveWhatsapp(): ?WhatsappGatewayInterface
    {
        $c        = PlatformSetting::messaging();
        $provider = $c['whatsapp_provider'] ?? 'twilio';

        return match ($provider) {
            'twilio'     => static::ifFilled(new TwilioWhatsappGateway($c['twilio'] ?? []), ['sid', 'token', 'whatsapp_from'], $c['twilio'] ?? []),
            'meta_cloud' => static::ifFilled(new MetaCloudWhatsappGateway($c['meta'] ?? []), ['token', 'phone_id'], $c['meta'] ?? []),
            default      => null,
        };
    }

    /**
     * Resolve the Telegram gateway when enabled and configured, or null.
     */
    public static function resolveTelegram(): ?TelegramGateway
    {
        $c = PlatformSetting::messaging();

        if (empty($c['telegram_enabled']) || empty($c['telegram']['bot_token'])) {
            return null;
        }

        return new TelegramGateway($c['telegram']);
    }

    public static function isSmsConfigured(): bool
    {
        return static::resolveSms() !== null;
    }

    public static function isWhatsappConfigured(): bool
    {
        return static::resolveWhatsapp() !== null;
    }

    public static function isTelegramConfigured(): bool
    {
        return static::resolveTelegram() !== null;
    }

    /**
     * Return the gateway only when all required config keys are non-empty.
     */
    private static function ifFilled(object $gateway, array $required, array $config): ?object
    {
        foreach ($required as $key) {
            if (empty($config[$key])) {
                return null;
            }
        }

        return $gateway;
    }
}
