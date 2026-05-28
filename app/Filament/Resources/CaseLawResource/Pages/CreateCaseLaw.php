<?php

namespace App\Filament\Resources\CaseLawResource\Pages;

use App\Filament\Resources\CaseLawResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCaseLaw extends CreateRecord
{
    protected static string $resource = CaseLawResource::class;

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
