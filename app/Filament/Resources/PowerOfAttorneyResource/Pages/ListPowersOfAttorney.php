<?php

namespace App\Filament\Resources\PowerOfAttorneyResource\Pages;

use App\Filament\Resources\PowerOfAttorneyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPowersOfAttorney extends ListRecords
{
    protected static string $resource = PowerOfAttorneyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة توكيل'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'     => Tab::make('الكل'),
            'active'  => Tab::make('نشطة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'active'))
                ->badge(fn () => \App\Models\PowerOfAttorney::withoutGlobalScopes()->where('status', 'active')->count())
                ->badgeColor('success'),
            'expired' => Tab::make('منتهية')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'expired')),
            'revoked' => Tab::make('ملغاة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'revoked')),
        ];
    }
}
