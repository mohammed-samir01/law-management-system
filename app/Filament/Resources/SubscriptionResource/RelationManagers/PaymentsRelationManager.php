<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title       = 'سجل المدفوعات';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('amount')->label('المبلغ')->money(fn ($record) => $record->currency),
                Tables\Columns\TextColumn::make('billing_cycle')->label('الدورة')
                    ->formatStateUsing(fn ($state) => $state === 'yearly' ? 'سنوي' : 'شهري'),
                Tables\Columns\TextColumn::make('gateway')->label('البوابة'),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                    ->formatStateUsing(fn ($record) => $record->status_label)
                    ->color(fn ($state) => match ($state) {
                        'completed' => 'success', 'pending' => 'warning', 'refunded' => 'info', default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('gateway_transaction_id')->label('رقم المعاملة')->placeholder('—')->limit(20),
                Tables\Columns\TextColumn::make('paid_at')->label('تاريخ الدفع')->dateTime('Y/m/d H:i')->placeholder('—'),
            ])
            ->paginated(false);
    }
}
