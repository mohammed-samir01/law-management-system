<?php

namespace App\Filament\Resources\EnforcementFileResource\Pages;

use App\Filament\Resources\EnforcementFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnforcementFile extends EditRecord
{
    protected static string $resource = EnforcementFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('عرض'),
            Actions\DeleteAction::make()->label('حذف'),
            Actions\RestoreAction::make()->label('استعادة'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
