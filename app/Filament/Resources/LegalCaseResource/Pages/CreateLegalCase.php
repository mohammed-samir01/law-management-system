<?php

namespace App\Filament\Resources\LegalCaseResource\Pages;

use App\Filament\Resources\LegalCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLegalCase extends CreateRecord
{
    protected static string $resource = LegalCaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['office_id'] = auth()->user()->office_id;
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
