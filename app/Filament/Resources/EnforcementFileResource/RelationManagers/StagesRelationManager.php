<?php

namespace App\Filament\Resources\EnforcementFileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StagesRelationManager extends RelationManager
{
    protected static string $relationship = 'stages';
    protected static ?string $title       = 'مراحل التنفيذ';
    protected static ?string $label       = 'مرحلة';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('stage_name.ar')
                ->label(__('enforcement.stage_name') . ' (عربي)')
                ->required(),

            Forms\Components\TextInput::make('stage_name.en')
                ->label(__('enforcement.stage_name') . ' (English)'),

            Forms\Components\TextInput::make('order')
                ->label(__('enforcement.stage_order'))
                ->numeric()
                ->default(0),

            Forms\Components\Select::make('status')
                ->label(__('enforcement.stage_status'))
                ->options([
                    'pending'     => 'معلق',
                    'in_progress' => 'جارٍ',
                    'completed'   => 'مكتمل',
                    'skipped'     => 'تخطّى',
                ])
                ->default('pending')
                ->required(),

            Forms\Components\DateTimePicker::make('completed_at')
                ->label(__('enforcement.completed_at'))
                ->nullable(),

            Forms\Components\Textarea::make('notes')
                ->label(__('enforcement.stage_notes'))
                ->nullable()
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('stage_name')
                    ->label(__('enforcement.stage_name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('stage_name', 'ar') ?: $record->getTranslation('stage_name', 'en')),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('enforcement.stage_status'))
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'pending'     => 'gray',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'skipped'     => 'info',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending'     => 'معلق',
                        'in_progress' => 'جارٍ',
                        'completed'   => 'مكتمل',
                        'skipped'     => 'تخطّى',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label(__('enforcement.completed_at'))
                    ->dateTime('Y/m/d')
                    ->default('—'),

                Tables\Columns\TextColumn::make('notes')
                    ->label(__('enforcement.stage_notes'))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة مرحلة'),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_complete')
                    ->label('إنهاء')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'completed')
                    ->action(fn ($record) => $record->update(['status' => 'completed', 'completed_at' => now()])),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([]);
    }
}
