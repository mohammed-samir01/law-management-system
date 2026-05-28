<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;

abstract class AbstractGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function successResponse(array $data = [], string $message = 'success'): array
    {
        return ['success' => true, 'message' => $message, 'data' => $data];
    }

    protected function errorResponse(string $message, array $data = []): array
    {
        return ['success' => false, 'message' => $message, 'data' => $data];
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        Log::$level('['.class_basename($this).'] '.$message, $context);
    }
}
