<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;

/**
 * InstaPay (Egypt) — peer-to-peer mobile wallet; no public merchant API.
 * Payments are confirmed manually by the office admin.
 */
class InstaPayGateway extends AbstractGateway
{
    public function charge(array $data): array
    {
        return $this->successResponse([
            'instructions' => [
                'ar' => 'يرجى تحويل المبلغ إلى حساب InstaPay: '.$this->getAccountId().' ثم إرسال صورة التأكيد.',
                'en' => 'Please transfer to InstaPay account: '.$this->getAccountId().' and send a confirmation screenshot.',
            ],
            'account_id'     => $this->getAccountId(),
            'amount'         => $data['amount'],
            'currency'       => $data['currency'] ?? 'EGP',
            'reference'      => $data['reference'] ?? null,
            'requires_confirmation' => true,
        ], 'Manual confirmation required');
    }

    public function refund(string $transactionId, float $amount): array
    {
        return $this->successResponse([
            'instructions' => [
                'ar' => 'يتم رد المبلغ يدوياً عبر InstaPay خلال 24 ساعة.',
                'en' => 'Refund will be processed manually via InstaPay within 24 hours.',
            ],
        ], 'Manual refund required');
    }

    public function getStatus(string $transactionId): string
    {
        return 'pending';
    }

    public function handleWebhook(Request $request): void
    {
        $this->log('info', 'InstaPay manual confirmation received', $request->all());
    }

    public function testConnection(): bool
    {
        return ! empty($this->config['account_id']);
    }

    private function getAccountId(): string
    {
        return $this->config['account_id'] ?? '';
    }
}
