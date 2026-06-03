<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymobGateway extends AbstractGateway
{
    private string $baseUrl = 'https://accept.paymob.com/api';

    public function charge(array $data): array
    {
        try {
            $token = $this->authenticate();
            if (! $token) {
                return $this->errorResponse('Paymob authentication failed');
            }

            $orderId = $this->createOrder($token, $data);
            $paymentToken = $this->requestPaymentKey($token, $orderId, $data);

            return $this->successResponse([
                'payment_url' => 'https://accept.paymob.com/api/acceptance/iframes/'.$this->config['iframe_id'].'?payment_token='.$paymentToken,
                'order_id'    => $orderId,
            ]);
        } catch (\Throwable $e) {
            $this->log('error', 'Charge failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function refund(string $transactionId, float $amount): array
    {
        try {
            $token = $this->authenticate();
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/acceptance/void_refund/refund", [
                    'auth_token'     => $token,
                    'transaction_id' => $transactionId,
                    'amount_cents'   => (int) ($amount * 100),
                ]);

            return $response->successful()
                ? $this->successResponse($response->json())
                : $this->errorResponse($response->json('message', 'Refund failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'Refund failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getStatus(string $transactionId): string
    {
        try {
            $token = $this->authenticate();
            $response = Http::get("{$this->baseUrl}/acceptance/transactions/{$transactionId}", [
                'token' => $token,
            ]);
            return $response->json('success') ? 'completed' : 'failed';
        } catch (\Throwable) {
            return 'unknown';
        }
    }

    public function handleWebhook(Request $request): void
    {
        $hmac = $request->query('hmac');
        $computed = $this->computeHmac($request->all());

        if (! hash_equals($computed, $hmac)) {
            $this->log('warning', 'Invalid HMAC on webhook');
            return;
        }

        $this->log('info', 'Paymob webhook received', $request->all());
    }

    public function testConnection(): bool
    {
        return (bool) $this->authenticate();
    }

    private function authenticate(): ?string
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->config['api_key'],
        ]);
        return $response->json('token');
    }

    private function createOrder(string $token, array $data): ?string
    {
        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token'     => $token,
            'delivery_needed' => false,
            'amount_cents'   => (int) ($data['amount'] * 100),
            'currency'       => $data['currency'] ?? 'EGP',
            'items'          => [],
        ]);
        return $response->json('id');
    }

    private function requestPaymentKey(string $token, string $orderId, array $data): ?string
    {
        $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
            'auth_token'     => $token,
            'amount_cents'   => (int) ($data['amount'] * 100),
            'expiration'     => 3600,
            'order_id'       => $orderId,
            'currency'       => $data['currency'] ?? 'EGP',
            'integration_id' => $this->config['integration_id'],
            'billing_data'   => [
                'apartment'     => 'N/A',
                'email'         => $data['email'] ?? 'client@mizan.test',
                'floor'         => 'N/A',
                'first_name'    => $data['first_name'] ?? 'Client',
                'street'        => 'N/A',
                'building'      => 'N/A',
                'phone_number'  => $data['phone'] ?? '+20100000000',
                'shipping_method' => 'N/A',
                'postal_code'   => 'N/A',
                'city'          => 'Cairo',
                'country'       => 'EG',
                'last_name'     => $data['last_name'] ?? '',
                'state'         => 'Cairo',
            ],
        ]);
        return $response->json('token');
    }

    private function computeHmac(array $data): string
    {
        $fields = ['amount_cents','created_at','currency','error_occured','has_parent_transaction','id',
                   'integration_id','is_3d_secure','is_auth','is_capture','is_refunded','is_standalone_payment',
                   'is_voided','order','owner','pending','source_data_pan','source_data_sub_type','source_data_type','success'];
        $str = '';
        foreach ($fields as $field) {
            $str .= $data[$field] ?? '';
        }
        return hash_hmac('sha512', $str, $this->config['hmac_secret'] ?? '');
    }
}
