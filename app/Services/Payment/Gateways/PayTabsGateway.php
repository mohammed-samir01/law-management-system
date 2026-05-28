<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayTabsGateway extends AbstractGateway
{
    private string $baseUrl = 'https://secure.paytabs.sa/payment/request';

    public function charge(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'authorization' => $this->config['server_key'],
            ])->post($this->baseUrl, [
                'profile_id'    => $this->config['profile_id'],
                'tran_type'     => 'sale',
                'tran_class'    => 'ecom',
                'cart_id'       => $data['reference'] ?? uniqid('amer_'),
                'cart_currency' => $data['currency'] ?? 'SAR',
                'cart_amount'   => $data['amount'],
                'cart_description' => $data['description'] ?? 'Invoice payment',
                'return'        => $data['return_url'] ?? config('app.url').'/payments/success',
                'callback'      => $data['callback_url'] ?? config('app.url').'/payments/webhook/paytabs',
                'customer_details' => [
                    'name'  => $data['customer_name'] ?? 'Client',
                    'email' => $data['email'] ?? 'client@amer.test',
                    'phone' => $data['phone'] ?? '',
                    'street1' => $data['address'] ?? 'N/A',
                    'city'  => $data['city'] ?? 'Riyadh',
                    'state' => $data['state'] ?? 'Riyadh',
                    'country' => $data['country'] ?? 'SA',
                    'zip'   => $data['zip'] ?? '12345',
                ],
            ]);

            if ($response->successful()) {
                return $this->successResponse([
                    'transaction_id' => $response->json('tran_ref'),
                    'payment_url'    => $response->json('redirect_url'),
                    'status'         => $response->json('payment_result.response_status'),
                ]);
            }

            return $this->errorResponse($response->json('message', 'Charge failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'PayTabs charge failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function refund(string $transactionId, float $amount): array
    {
        try {
            $response = Http::withHeaders([
                'authorization' => $this->config['server_key'],
            ])->post('https://secure.paytabs.sa/payment/request', [
                'profile_id' => $this->config['profile_id'],
                'tran_type'  => 'refund',
                'tran_class' => 'ecom',
                'tran_ref'   => $transactionId,
                'cart_id'    => 'refund_'.time(),
                'cart_currency' => 'SAR',
                'cart_amount'   => $amount,
                'cart_description' => 'Refund',
            ]);

            return $response->successful()
                ? $this->successResponse($response->json())
                : $this->errorResponse($response->json('message', 'Refund failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'PayTabs refund failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getStatus(string $transactionId): string
    {
        try {
            $response = Http::withHeaders([
                'authorization' => $this->config['server_key'],
            ])->post('https://secure.paytabs.sa/payment/query', [
                'profile_id' => $this->config['profile_id'],
                'tran_ref'   => $transactionId,
            ]);

            return match($response->json('payment_result.response_status')) {
                'A' => 'completed',
                'D' => 'failed',
                'H' => 'pending',
                default => 'pending',
            };
        } catch (\Throwable) {
            return 'unknown';
        }
    }

    public function handleWebhook(Request $request): void
    {
        $this->log('info', 'PayTabs webhook received', $request->all());
    }

    public function testConnection(): bool
    {
        return ! empty($this->config['server_key']) && ! empty($this->config['profile_id']);
    }
}
