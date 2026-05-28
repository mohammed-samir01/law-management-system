<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StripeGateway extends AbstractGateway
{
    private string $baseUrl = 'https://api.stripe.com/v1';

    public function charge(array $data): array
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->asForm()
                ->post("{$this->baseUrl}/payment_intents", [
                    'amount'               => (int) ($data['amount'] * 100),
                    'currency'             => strtolower($data['currency'] ?? 'usd'),
                    'description'          => $data['description'] ?? 'Invoice payment',
                    'automatic_payment_methods' => ['enabled' => 'true'],
                ]);

            if ($response->successful()) {
                return $this->successResponse([
                    'transaction_id' => $response->json('id'),
                    'client_secret'  => $response->json('client_secret'),
                    'status'         => $response->json('status'),
                ]);
            }

            return $this->errorResponse($response->json('error.message', 'Charge failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'Charge failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function refund(string $transactionId, float $amount): array
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->asForm()
                ->post("{$this->baseUrl}/refunds", [
                    'payment_intent' => $transactionId,
                    'amount'         => (int) ($amount * 100),
                ]);

            return $response->successful()
                ? $this->successResponse($response->json())
                : $this->errorResponse($response->json('error.message', 'Refund failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'Refund failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getStatus(string $transactionId): string
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->get("{$this->baseUrl}/payment_intents/{$transactionId}");

            return match($response->json('status')) {
                'succeeded'                => 'completed',
                'canceled'                 => 'failed',
                'requires_payment_method'  => 'pending',
                default                    => 'pending',
            };
        } catch (\Throwable) {
            return 'unknown';
        }
    }

    public function handleWebhook(Request $request): void
    {
        $secret    = $this->config['webhook_secret'] ?? '';
        $signature = $request->header('Stripe-Signature', '');

        if ($secret) {
            $payload   = $request->getContent();
            $timestamp = explode(',', explode('t=', $signature)[1] ?? '')[0];
            $computed  = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

            if (! str_contains($signature, $computed)) {
                $this->log('warning', 'Invalid Stripe webhook signature');
                return;
            }
        }

        $this->log('info', 'Stripe webhook received', ['type' => $request->json('type')]);
    }

    public function testConnection(): bool
    {
        try {
            $response = Http::withBasicAuth($this->config['secret_key'], '')
                ->get("{$this->baseUrl}/account");
            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
