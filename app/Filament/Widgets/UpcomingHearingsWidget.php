<?php

namespace App\Filament\Widgets;

use App\Models\Hearing;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingHearingsWidget extends BaseWidget
{
    protected static ?string $heading = 'الجلسات القادمة';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hearing::query()
                    ->with(['legalCase'])
                    ->where('status', 'scheduled')
                    ->where('scheduled_at', '>=', now())
                    ->orderBy('scheduled_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('الموعد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('legalCase.case_number')
                    ->label('رقم القضية'),
                Tables\Columns\TextColumn::make('legalCase.title')
                    ->label('القضية')
                    ->getStateUsing(fn ($record) => $record->legalCase?->getTranslation('title', 'ar')),
                Tables\Columns\TextColumn::make('location')
                    ->label('الموقع'),
                Tables\Columns\TextColumn::make('judge')
                    ->label('القاضي'),
            ]);
    }
}
