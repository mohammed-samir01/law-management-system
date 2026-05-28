<?php

namespace App\Filament\Resources\SupportTicketResource\Pages;

use App\Filament\Resources\SupportTicketResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('تعديل'),

            Actions\Action::make('assign_me')
                ->label('إسناد لي')
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->visible(fn () => ! $this->record->assigned_to)
                ->action(function () {
                    $this->record->update([
                        'assigned_to' => auth()->id(),
                        'status'      => 'in_progress',
                    ]);
                    $this->refreshFormData(['status', 'assigned_to']);
                }),

            Actions\Action::make('close')
                ->label('إغلاق التذكرة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => ! in_array($this->record->status, ['closed', 'resolved']))
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('resolve')
                ->label('تحديد كمحلولة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'in_progress')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'resolved']);
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('reopen')
                ->label('إعادة فتح')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['closed', 'resolved']))
                ->action(function () {
                    $this->record->update(['status' => 'open']);
                    $this->refreshFormData(['status']);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('تفاصيل التذكرة')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('العنوان')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('category')
                            ->label('الفئة')
                            ->badge()
                            ->color('gray')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'technical' => 'تقني',
                                'billing'   => 'مالي',
                                'legal'     => 'قانوني',
                                default     => 'عام',
                            }),

                        Infolists\Components\TextEntry::make('priority')
                            ->label('الأولوية')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'urgent' => 'danger',
                                'high'   => 'warning',
                                'medium' => 'info',
                                'low'    => 'gray',
                                default  => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match($state) {
                                'urgent' => 'عاجلة',
                                'high'   => 'عالية',
                                'medium' => 'متوسطة',
                                'low'    => 'منخفضة',
                                default  => $state,
                            }),

                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'open'        => 'danger',
                                'in_progress' => 'warning',
                                'resolved'    => 'success',
                                'closed'      => 'gray',
                                default       => 'gray',
                            })
                            ->formatStateUsing(fn ($record) => $record->status_label),

                        Infolists\Components\TextEntry::make('assignedTo.name')
                            ->label('مُسند إلى')
                            ->default('غير مُسند'),

                        Infolists\Components\TextEntry::make('createdBy.name')
                            ->label('أُنشئت بواسطة'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الفتح')
                            ->dateTime('Y/m/d H:i'),
                    ])->columns(3),
            ]);
    }
}
