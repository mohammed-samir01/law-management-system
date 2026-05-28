<?php

namespace App\Filament\Widgets;

use App\Models\LegalCase;
use App\Models\Payment;
use App\Models\Hearing;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int    $sort    = 4;
    protected static ?string $heading = 'آخر الجلسات المجدولة';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hearing::query()
                    ->with(['legalCase'])
                    ->where('scheduled_at', '>=', now())
                    ->where('status', 'scheduled')
                    ->orderBy('scheduled_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('legalCase.case_number')
                    ->label('رقم القضية')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('موعد الجلسة')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('المكان')
                    ->limit(30),

                Tables\Columns\TextColumn::make('court_room')
                    ->label('قاعة المحكمة')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'scheduled'  => 'info',
                        'held'       => 'success',
                        'adjourned'  => 'warning',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'scheduled' => 'مجدولة',
                        'held'      => 'عُقدت',
                        'adjourned' => 'مؤجلة',
                        'cancelled' => 'ملغاة',
                        default     => $state,
                    }),
            ])
            ->paginated(false);
    }
}
