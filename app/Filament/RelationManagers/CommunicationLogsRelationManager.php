<?php

namespace App\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Shared relation manager — usable on any owner that exposes a `communications`
 * HasMany (LegalCase and Client). The owner FK (case_id / client_id) is set
 * automatically by Filament from the relationship.
 */
class CommunicationLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'communications';
    protected static ?string $title       = 'سجل التواصل';
    protected static ?string $label        = 'تواصل';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label(__('comm.type'))
                ->options([
                    'call'     => __('comm.type_call'),
                    'email'    => __('comm.type_email'),
                    'whatsapp' => __('comm.type_whatsapp'),
                    'sms'      => __('comm.type_sms'),
                    'meeting'  => __('comm.type_meeting'),
                    'other'    => __('comm.type_other'),
                ])->default('call')->required(),
            Forms\Components\Select::make('direction')
                ->label(__('comm.direction'))
                ->options([
                    'incoming' => __('comm.incoming'),
                    'outgoing' => __('comm.outgoing'),
                ])->nullable(),
            Forms\Components\DateTimePicker::make('occurred_at')
                ->label(__('comm.occurred_at'))->default(now())->required(),
            Forms\Components\TextInput::make('subject')
                ->label(__('comm.subject'))->columnSpanFull(),
            Forms\Components\Textarea::make('summary')
                ->label(__('comm.summary'))->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')->label(__('comm.occurred_at'))->dateTime('Y/m/d H:i')->sortable(),
                Tables\Columns\TextColumn::make('type')->label(__('comm.type'))->badge()
                    ->formatStateUsing(fn ($state) => __('comm.type_' . $state)),
                Tables\Columns\TextColumn::make('direction')->label(__('comm.direction'))->default('—')
                    ->formatStateUsing(fn ($state) => $state ? __('comm.' . $state) : '—'),
                Tables\Columns\TextColumn::make('subject')->label(__('comm.subject'))->limit(40)->default('—'),
                Tables\Columns\TextColumn::make('createdBy.name')->label(__('comm.by'))->default('—')->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('comm.add')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ]);
    }
}
