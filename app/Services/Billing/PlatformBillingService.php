<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\PlatformSetting;
use App\Services\Payment\Gateways\PaymobGateway;
use App\Services\Payment\Gateways\StripeGateway;
use App\Services\Payment\PaymentGatewayInterface;
use RuntimeException;

/**
 * Platform-level billing — charges OFFICES for their Mizan subscriptions.
 *
 * Reuses the existing gateway classes (PaymobGateway / StripeGateway). The
 * gateway choice and API keys are managed dynamically from the dashboard
 * (PlatformSetting, encrypted), falling back to config/services (.env).
 */
class PlatformBillingService
{
    private static array $map = [
        'paymob' => PaymobGateway::class,
        'stripe' => StripeGateway::class,
    ];

    public static function gatewayName(): string
    {
        return PlatformSetting::billing()['gateway'] ?: 'paymob';
    }

    public static function gateway(): PaymentGatewayInterface
    {
        $billing = PlatformSetting::billing();
        $name    = $billing['gateway'] ?: 'paymob';
        $class   = self::$map[$name] ?? null;
        $config  = $billing['config'];

        if (! $class) {
            throw new RuntimeException("بوابة دفع المنصة غير معروفة: {$name}");
        }

        if (empty($config)) {
            throw new RuntimeException('لم يتم إعداد بوابة دفع المنصة. يرجى ضبطها من لوحة التحكم: الإعدادات ← بوابة دفع المنصة.');
        }

        $config['test_mode'] = $billing['test_mode'];

        return new $class($config);
    }

    public static function isConfigured(): bool
    {
        return ! empty(PlatformSetting::billing()['config']);
    }

    public static function priceFor(Plan $plan, string $cycle): float
    {
        return $plan->priceFor($cycle);
    }
}
