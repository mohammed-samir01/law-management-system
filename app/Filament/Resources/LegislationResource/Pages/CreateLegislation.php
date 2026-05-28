<?php

namespace App\Filament\Resources\LegislationResource\Pages;

use App\Filament\Resources\LegislationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLegislation extends CreateRecord
{
    protected static string $resource = LegislationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['office_id'] = auth()->user()->office_id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
