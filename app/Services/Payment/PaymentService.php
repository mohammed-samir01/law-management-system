<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Services\Payment\Gateways\BankTransferGateway;
use App\Services\Payment\Gateways\InstaPayGateway;
use App\Services\Payment\Gateways\MadaGateway;
use App\Services\Payment\Gateways\MoyasarGateway;
use App\Services\Payment\Gateways\PaymobGateway;
use App\Services\Payment\Gateways\PayPalGateway;
use App\Services\Payment\Gateways\PayTabsGateway;
use App\Services\Payment\Gateways\StripeGateway;
use App\Services\Payment\Gateways\VodafoneCashGateway;
use InvalidArgumentException;

class PaymentService
{
    private static array $map = [
        'paymob'        => PaymobGateway::class,
        'instapay'      => InstaPayGateway::class,
        'vodafone_cash' => VodafoneCashGateway::class,
        'moyasar'       => MoyasarGateway::class,
        'mada'          => MadaGateway::class,
        'paytabs'       => PayTabsGateway::class,
        'stripe'        => StripeGateway::class,
        'paypal'        => PayPalGateway::class,
        'bank_transfer' => BankTransferGateway::class,
    ];

    public static function gateway(string $gatewayName, int $officeId): PaymentGatewayInterface
    {
        $model = PaymentGateway::withoutGlobalScopes()
            ->where('office_id', $officeId)
            ->where('gateway_name', $gatewayName)
            ->where('is_active', true)
            ->firstOrFail();

        $class = self::$map[$gatewayName] ?? null;
        if (! $class) {
            throw new InvalidArgumentException("Unknown payment gateway: {$gatewayName}");
        }

        $config = $model->decrypted_config;
        $config['test_mode'] = $model->test_mode;

        return new $class($config);
    }

    public static function fromModel(PaymentGateway $model): PaymentGatewayInterface
    {
        $class = self::$map[$model->gateway_name] ?? null;
        if (! $class) {
            throw new InvalidArgumentException("Unknown payment gateway: {$model->gateway_name}");
        }

        $config = $model->decrypted_config;
        $config['test_mode'] = $model->test_mode;

        return new $class($config);
    }
}
