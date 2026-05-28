<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalGateway extends AbstractGateway
{
    private string $baseUrl;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->baseUrl = ($config['test_mode'] ?? false)
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function charge(array $data): array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent'         => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => strtoupper($data['currency'] ?? 'USD'),
                            'value'         => number_format($data['amount'], 2, '.', ''),
                        ],
                        'description' => $data['description'] ?? 'Invoice payment',
                    ]],
                    'application_context' => [
                        'return_url' => $data['return_url'] ?? config('app.url').'/payments/success',
                        'cancel_url' => $data['cancel_url'] ?? config('app.url').'/payments/cancel',
                    ],
                ]);

            if ($response->successful()) {
                $order = $response->json();
                $approveUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                return $this->successResponse([
                    'transaction_id' => $order['id'],
                    'payment_url'    => $approveUrl,
                    'status'         => $order['status'],
                ]);
            }

            return $this->errorResponse($response->json('message', 'Charge failed'));
        } catch (\Throwable $e) {
            $this->log('error', 'Charge failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function refund(string $transactionId, float $amount): array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/v2/payments/captures/{$transactionId}/refund", [
                    'amount' => [
                        'value'         => number_format($amount, 2, '.', ''),
                        'currency_code' => 'USD',
                    ],
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
            $token    = $this->getAccessToken();
            $response = Http::withToken($token)->get("{$this->baseUrl}/v2/checkout/orders/{$transactionId}");

            return match($response->json('status')) {
                'COMPLETED' => 'completed',
                'VOIDED'    => 'failed',
                default     => 'pending',
            };
        } catch (\Throwable) {
            return 'unknown';
        }
    }

    public function handleWebhook(Request $request): void
    {
        $this->log('info', 'PayPal webhook received', ['event_type' => $request->json('event_type')]);
    }

    public function testConnection(): bool
    {
        return (bool) $this->getAccessToken();
    }

    private function getAccessToken(): ?string
    {
        $response = Http::withBasicAuth($this->config['client_id'], $this->config['client_secret'])
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        return $response->json('access_token');
    }
}
