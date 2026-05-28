<?php

namespace App\Filament\Resources\EnforcementFileResource\Pages;

use App\Filament\Resources\EnforcementFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEnforcementFiles extends ListRecords
{
    protected static string $resource = EnforcementFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة ملف تنفيذ'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'       => Tab::make('الكل'),
            'active'    => Tab::make('نشطة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'active'))
                ->badge(fn () => \App\Models\EnforcementFile::where('status', 'active')->count())
                ->badgeColor('success'),
            'completed' => Tab::make('منتهية')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'completed')),
            'withdrawn' => Tab::make('مسحوبة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'withdrawn')),
        ];
    }
}
