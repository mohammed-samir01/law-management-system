<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function charge(array $data): array;
    public function refund(string $transactionId, float $amount): array;
    public function getStatus(string $transactionId): string;
    public function handleWebhook(Request $request): void;
    public function testConnection(): bool;
}
