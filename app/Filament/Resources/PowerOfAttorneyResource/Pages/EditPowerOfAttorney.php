<?php

namespace App\Filament\Resources\PowerOfAttorneyResource\Pages;

use App\Filament\Resources\PowerOfAttorneyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPowerOfAttorney extends EditRecord
{
    protected static string $resource = PowerOfAttorneyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('حذف'),
            Actions\RestoreAction::make()->label('استعادة'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
