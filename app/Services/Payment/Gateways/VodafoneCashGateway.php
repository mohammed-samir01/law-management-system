<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;

/**
 * Vodafone Cash (Egypt) — mobile wallet; merchant API requires a business agreement.
 * Payments are confirmed manually until an API contract is established.
 */
class VodafoneCashGateway extends AbstractGateway
{
    public function charge(array $data): array
    {
        return $this->successResponse([
            'instructions' => [
                'ar' => 'يرجى تحويل المبلغ '.($data['amount'] ?? '').(' '.$data['currency'] ?? ' EGP').' إلى رقم Vodafone Cash: '.$this->getMerchantNumber().'.',
                'en' => 'Please transfer '.($data['amount'] ?? '').' '.($data['currency'] ?? 'EGP').' to Vodafone Cash number: '.$this->getMerchantNumber().'.',
            ],
            'merchant_number'        => $this->getMerchantNumber(),
            'amount'                 => $data['amount'],
            'currency'               => $data['currency'] ?? 'EGP',
            'requires_confirmation'  => true,
        ], 'Manual confirmation required');
    }

    public function refund(string $transactionId, float $amount): array
    {
        return $this->successResponse([
            'instructions' => [
                'ar' => 'يتم رد المبلغ يدوياً عبر Vodafone Cash خلال 24 ساعة.',
                'en' => 'Refund will be processed manually via Vodafone Cash within 24 hours.',
            ],
        ], 'Manual refund required');
    }

    public function getStatus(string $transactionId): string
    {
        return 'pending';
    }

    public function handleWebhook(Request $request): void
    {
        $this->log('info', 'Vodafone Cash manual confirmation received', $request->all());
    }

    public function testConnection(): bool
    {
        return ! empty($this->config['merchant_number']);
    }

    private function getMerchantNumber(): string
    {
        return $this->config['merchant_number'] ?? '';
    }
}
