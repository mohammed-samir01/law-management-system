<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Services\Payment\Gateways\PaymobGateway;
use App\Services\Payment\Gateways\StripeGateway;
use App\Services\Payment\PaymentGatewayInterface;
use RuntimeException;

/**
 * Platform-level billing — charges OFFICES for their Mizan subscriptions.
 *
 * Reuses the existing gateway classes (PaymobGateway / StripeGateway) but
 * instantiates them with the PLATFORM's own credentials from config/services
 * instead of a per-office DB record.
 */
class PlatformBillingService
{
    private static array $map = [
        'paymob' => PaymobGateway::class,
        'stripe' => StripeGateway::class,
    ];

    public static function gatewayName(): string
    {
        return config('services.platform_billing.gateway', 'paymob');
    }

    public static function gateway(): PaymentGatewayInterface
    {
        $name   = self::gatewayName();
        $class  = self::$map[$name] ?? null;
        $config = config("services.platform_billing.$name", []);

        if (! $class) {
            throw new RuntimeException("بوابة دفع المنصة غير معروفة: {$name}");
        }

        if (empty(array_filter($config))) {
            throw new RuntimeException('لم يتم إعداد مفاتيح بوابة دفع المنصة في إعدادات السيرفر.');
        }

        return new $class($config);
    }

    public static function isConfigured(): bool
    {
        $config = config('services.platform_billing.' . self::gatewayName(), []);

        return ! empty(array_filter($config));
    }

    public static function priceFor(Plan $plan, string $cycle): float
    {
        return $plan->priceFor($cycle);
    }
}
