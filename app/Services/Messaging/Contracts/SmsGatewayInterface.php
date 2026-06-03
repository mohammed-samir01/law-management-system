<?php

namespace App\Services\Messaging\Contracts;

interface SmsGatewayInterface
{
    /**
     * Send an SMS. Returns ['success' => bool, 'message' => string, 'data' => array].
     */
    public function send(string $to, string $body): array;

    /**
     * Verify the configured credentials are valid.
     */
    public function test(): bool;
}
