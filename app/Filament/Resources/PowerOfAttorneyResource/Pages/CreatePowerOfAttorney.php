<?php

namespace App\Filament\Resources\PowerOfAttorneyResource\Pages;

use App\Filament\Resources\PowerOfAttorneyResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePowerOfAttorney extends CreateRecord
{
    protected static string $resource = PowerOfAttorneyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['office_id']  = auth()->user()->office_id;
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
