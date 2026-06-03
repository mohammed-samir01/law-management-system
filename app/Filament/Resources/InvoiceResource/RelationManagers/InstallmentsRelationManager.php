<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installmentPlan';
    protected static ?string $title       = 'الأقساط';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (auth()->user()?->office?->hasAddon('fee-installments') ?? false)
            && $ownerRecord->installmentPlan()->exists();
    }

    public function table(Table $table): Table
    {
        // The owner relationship is a HasOne plan; show its installments.
        return $table
            ->query(fn () => \App\Models\Installment::query()
                ->where('installment_plan_id', $this->getOwnerRecord()->installmentPlan?->id ?? 0)
                ->orderBy('sequence'))
            ->columns([
                Tables\Columns\TextColumn::make('sequence')->label('#'),
                Tables\Columns\TextColumn::make('amount')->label(__('addons.inst_amount'))
                    ->money(fn () => $this->getOwnerRecord()->currency ?? 'EGP'),
                Tables\Columns\TextColumn::make('due_date')->label(__('addons.inst_due_date'))->date('Y/m/d'),
                Tables\Columns\TextColumn::make('status')->label(__('addons.inst_status'))->badge()
                    ->color(fn ($state) => match($state) { 'paid' => 'success', 'overdue' => 'danger', default => 'warning' })
                    ->formatStateUsing(fn ($state) => __('addons.inst_status_' . $state)),
                Tables\Columns\TextColumn::make('paid_at')->label('تاريخ الدفع')->dateTime('Y/m/d')->default('—'),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label(__('addons.inst_mark_paid'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'paid')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->markPaid()),
            ]);
    }
}
