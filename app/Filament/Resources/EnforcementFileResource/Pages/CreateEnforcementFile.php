<?php

namespace App\Filament\Resources\EnforcementFileResource\Pages;

use App\Filament\Resources\EnforcementFileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEnforcementFile extends CreateRecord
{
    protected static string $resource = EnforcementFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['office_id']  = auth()->user()->office_id;
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
