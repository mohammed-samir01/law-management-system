<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Mada (Saudi Arabia) — routed through Moyasar with mada card source type.
 */
class MadaGateway extends AbstractGateway
{
    private string $baseUrl = 'https://api.moyasar.com/v1';

    public function charge(array $data): array
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->post("{$this->baseUrl}/payments", [
                    'amount'       => (int) ($data['amount'] * 100),
                    'currency'     => $data['currency'] ?? 'SAR',
                    'description'  => $data['description'] ?? 'Invoice payment',
                    'callback_url' => $data['callback_url'] ?? config('app.url').'/payments/callback',
                    'source'       => [
                        'type'   => 'creditcard',
                        'name'   => $data['card_name'] ?? '',
                        'number' => $data['card_number'] ?? '',
                        'cvc'    => $data['card_cvc'] ?? '',
                        'month'  => $data['card_month'] ?? '',
                        'year'   => $data['card_year'] ?? '',
                        'manual' => 'false',
                    ],
                ]);

            if ($response->successful()) {
                $payment = $response->json();
                return $this->successResponse([
                    'transaction_id' => $payment['id'],
                    'status'         => $payment['status'],
                    'payment_url'    => $payment['source']['transaction_url'] ?? null,
                ]);
            }

            return $this->errorResponse($response->json('message', 'Charge failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'Mada charge failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function refund(string $transactionId, float $amount): array
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->post("{$this->baseUrl}/payments/{$transactionId}/refund", [
                    'amount' => (int) ($amount * 100),
                ]);

            return $response->successful()
                ? $this->successResponse($response->json())
                : $this->errorResponse($response->json('message', 'Refund failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'Mada refund failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getStatus(string $transactionId): string
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->get("{$this->baseUrl}/payments/{$transactionId}");

            return match($response->json('status')) {
                'paid'     => 'completed',
                'failed'   => 'failed',
                'refunded' => 'refunded',
                default    => 'pending',
            };
        } catch (\Throwable) {
            return 'unknown';
        }
    }

    public function handleWebhook(Request $request): void
    {
        $this->log('info', 'Mada webhook received', $request->all());
    }

    public function testConnection(): bool
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->get("{$this->baseUrl}/payments?per_page=1");
            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
