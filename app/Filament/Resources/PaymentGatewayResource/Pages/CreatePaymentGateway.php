<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use App\Filament\Resources\PaymentGatewayResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentGateway extends CreateRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['office_id']  = auth()->user()->office_id;
        $data['created_by'] = auth()->id();
        unset($data['config_fields']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
