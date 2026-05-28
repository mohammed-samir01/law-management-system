<?php

namespace App\Filament\Resources\HearingResource\Pages;

use App\Filament\Resources\HearingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHearing extends CreateRecord
{
    protected static string $resource = HearingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['office_id'] = auth()->user()->office_id;
        $data['created_by'] = auth()->id();
        return $data;
    }
}
