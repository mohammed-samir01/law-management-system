<?php

namespace App\Filament\Resources\EnforcementFileResource\Pages;

use App\Filament\Resources\EnforcementFileResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEnforcementFile extends ViewRecord
{
    protected static string $resource = EnforcementFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('تعديل'),
            Actions\Action::make('complete')
                ->label('إنهاء التنفيذ')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'active')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'completed']);
                    $this->refreshFormData(['status']);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('تفاصيل الملف')
                ->schema([
                    Infolists\Components\TextEntry::make('file_number')
                        ->label(__('enforcement.file_number'))
                        ->weight('bold'),

                    Infolists\Components\TextEntry::make('status')
                        ->label(__('enforcement.status'))
                        ->badge()
                        ->color(fn ($state) => match($state) {
                            'active'    => 'success',
                            'completed' => 'gray',
                            'withdrawn' => 'warning',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => match($state) {
                            'active'    => __('enforcement.statuses.active'),
                            'completed' => __('enforcement.statuses.completed'),
                            'withdrawn' => __('enforcement.statuses.withdrawn'),
                            default     => $state,
                        }),

                    Infolists\Components\TextEntry::make('title')
                        ->label(__('enforcement.title'))
                        ->getStateUsing(fn ($record) => $record->getTranslation('title', 'ar') ?: $record->getTranslation('title', 'en')),

                    Infolists\Components\TextEntry::make('enforcement_office')
                        ->label(__('enforcement.enforcement_office'))
                        ->default('—'),

                    Infolists\Components\TextEntry::make('debtor_name')
                        ->label(__('enforcement.debtor_name'))
                        ->getStateUsing(fn ($record) => $record->getTranslation('debtor_name', 'ar')),

                    Infolists\Components\TextEntry::make('creditor_name')
                        ->label(__('enforcement.creditor_name'))
                        ->getStateUsing(fn ($record) => $record->getTranslation('creditor_name', 'ar')),

                    Infolists\Components\TextEntry::make('debt_amount')
                        ->label(__('enforcement.debt_amount'))
                        ->money(fn ($record) => $record->currency),

                    Infolists\Components\TextEntry::make('created_at')
                        ->label('تاريخ الإضافة')
                        ->dateTime('Y/m/d'),
                ])->columns(3),
        ]);
    }
}
