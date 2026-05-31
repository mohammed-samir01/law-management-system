<?php

namespace App\Filament\Resources\PlatformLeadResource\Pages;

use App\Filament\Resources\PlatformLeadResource;
use Filament\Resources\Pages\EditRecord;

class EditPlatformLead extends EditRecord
{
    protected static string $resource = PlatformLeadResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
