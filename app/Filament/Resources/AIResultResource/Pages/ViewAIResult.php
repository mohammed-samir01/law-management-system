<?php

namespace App\Filament\Resources\AIResultResource\Pages;

use App\Filament\Resources\AIResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAIResult extends ViewRecord
{
    protected static string $resource = AIResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('حذف'),
        ];
    }
}
