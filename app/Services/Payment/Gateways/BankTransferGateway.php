<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\AbstractGateway;
use Illuminate\Http\Request;

/**
 * Manual bank transfer — admin confirms payment after receipt of bank notification.
 */
class BankTransferGateway extends AbstractGateway
{
    public function charge(array $data): array
    {
        return $this->successResponse([
            'instructions' => [
                'ar' => 'يرجى تحويل المبلغ إلى الحساب البنكي التالي: '."\n"
                    .'اسم البنك: '.($this->config['bank_name'] ?? '')."\n"
                    .'اسم الحساب: '.($this->config['account_name'] ?? '')."\n"
                    .'رقم الحساب: '.($this->config['account_number'] ?? '')."\n"
                    .'IBAN: '.($this->config['iban'] ?? '')."\n"
                    .'سويفت: '.($this->config['swift'] ?? ''),
                'en' => 'Please transfer the amount to the following bank account: '."\n"
                    .'Bank: '.($this->config['bank_name'] ?? '')."\n"
                    .'Account Name: '.($this->config['account_name'] ?? '')."\n"
                    .'Account Number: '.($this->config['account_number'] ?? '')."\n"
                    .'IBAN: '.($this->config['iban'] ?? '')."\n"
                    .'SWIFT: '.($this->config['swift'] ?? ''),
            ],
            'bank_name'      => $this->config['bank_name'] ?? '',
            'account_name'   => $this->config['account_name'] ?? '',
            'account_number' => $this->config['account_number'] ?? '',
            'iban'           => $this->config['iban'] ?? '',
            'swift'          => $this->config['swift'] ?? '',
            'amount'         => $data['amount'],
            'currency'       => $data['currency'] ?? 'EGP',
            'requires_confirmation' => true,
        ], 'Manual bank transfer — awaiting admin confirmation');
    }

    public function refund(string $transactionId, float $amount): array
    {
        return $this->successResponse([], 'Manual refund — contact office admin');
    }

    public function getStatus(string $transactionId): string
    {
        return 'pending';
    }

    public function handleWebhook(Request $request): void
    {
        $this->log('info', 'Bank transfer confirmation received', $request->all());
    }

    public function testConnection(): bool
    {
        return ! empty($this->config['account_number']) || ! empty($this->config['iban']);
    }
}
