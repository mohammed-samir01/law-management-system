<?php

namespace App\Filament\Resources\CaseLawResource\Pages;

use App\Filament\Resources\CaseLawResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaseLaw extends EditRecord
{
    protected static string $resource = CaseLawResource::class;

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
