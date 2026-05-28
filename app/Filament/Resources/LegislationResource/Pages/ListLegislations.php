<?php

namespace App\Filament\Resources\LegislationResource\Pages;

use App\Filament\Resources\LegislationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLegislations extends ListRecords
{
    protected static string $resource = LegislationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة تشريع'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'      => Tab::make('الكل'),
            'active'   => Tab::make('سارية')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'active')),
            'amended'  => Tab::make('معدّلة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'amended')),
            'repealed' => Tab::make('ملغاة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'repealed')),
        ];
    }
}
