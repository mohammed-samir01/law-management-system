<?php

namespace App\Filament\Resources\SupportTicketResource\Pages;

use App\Filament\Resources\SupportTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('فتح تذكرة جديدة'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل'),

            'open' => Tab::make('مفتوحة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'open'))
                ->badge(fn () => \App\Models\SupportTicket::where('status', 'open')->count())
                ->badgeColor('danger'),

            'in_progress' => Tab::make('قيد المعالجة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'in_progress'))
                ->badge(fn () => \App\Models\SupportTicket::where('status', 'in_progress')->count())
                ->badgeColor('warning'),

            'resolved' => Tab::make('محلولة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'resolved')),

            'closed' => Tab::make('مغلقة')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'closed')),
        ];
    }
}
