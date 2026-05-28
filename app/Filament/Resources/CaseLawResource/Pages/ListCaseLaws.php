<?php

namespace App\Filament\Resources\CaseLawResource\Pages;

use App\Filament\Resources\CaseLawResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCaseLaws extends ListRecords
{
    protected static string $resource = CaseLawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة سابقة قضائية'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'       => Tab::make('الكل'),
            'published' => Tab::make('منشورة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'published')),
            'draft'     => Tab::make('مسودة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'draft')),
            'archived'  => Tab::make('مؤرشفة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'archived')),
        ];
    }
}
