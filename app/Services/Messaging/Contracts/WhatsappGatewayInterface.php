<?php

namespace App\Services\Messaging\Contracts;

interface WhatsappGatewayInterface
{
    /**
     * Send a WhatsApp message. Returns ['success' => bool, 'message' => string, 'data' => array].
     */
    public function send(string $to, string $body): array;

    /**
     * Verify the configured credentials are valid.
     */
    public function test(): bool;
}
