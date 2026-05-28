<?php

namespace App\Filament\Resources\AIResultResource\Pages;

use App\Filament\Resources\AIResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAIResults extends ListRecords
{
    protected static string $resource = AIResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('طلب تحليل جديد'),
        ];
    }
}
