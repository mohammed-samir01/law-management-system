<?php

namespace App\Filament\Resources\DocumentTemplateResource\Pages;

use App\Filament\Resources\DocumentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDocumentTemplates extends ListRecords
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('قالب جديد'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل'),
            'active' => Tab::make('مفعّل')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_active', true)),
            'inactive' => Tab::make('معطّل')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_active', false)),
        ];
    }
}
