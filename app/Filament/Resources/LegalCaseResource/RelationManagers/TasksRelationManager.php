<?php

namespace App\Filament\Resources\LegalCaseResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';
    protected static ?string $title       = 'المهام';
    protected static ?string $label        = 'مهمة';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label(__('tasks.title'))->required()->columnSpanFull(),
            Forms\Components\Textarea::make('description')
                ->label(__('tasks.description'))->nullable()->columnSpanFull(),
            Forms\Components\Select::make('assigned_to')
                ->label(__('tasks.assigned_to'))
                ->relationship('assignee', 'name')
                ->searchable()->preload(),
            Forms\Components\DatePicker::make('due_date')->label(__('tasks.due_date')),
            Forms\Components\Select::make('priority')
                ->label(__('tasks.priority'))
                ->options([
                    'low'    => __('tasks.priority_low'),
                    'medium' => __('tasks.priority_medium'),
                    'high'   => __('tasks.priority_high'),
                ])->default('medium')->required(),
            Forms\Components\Select::make('status')
                ->label(__('tasks.status'))
                ->options([
                    'todo'        => __('tasks.status_todo'),
                    'in_progress' => __('tasks.status_in_progress'),
                    'done'        => __('tasks.status_done'),
                ])->default('todo')->required(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('due_date')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('tasks.title'))->wrap()->searchable(),
                Tables\Columns\TextColumn::make('assignee.name')->label(__('tasks.assigned_to'))->default('—'),
                Tables\Columns\TextColumn::make('due_date')->label(__('tasks.due_date'))
                    ->date('Y/m/d')->default('—')
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('priority')->label(__('tasks.priority'))->badge()
                    ->color(fn ($state) => match($state) { 'high' => 'danger', 'medium' => 'warning', default => 'gray' })
                    ->formatStateUsing(fn ($state) => __('tasks.priority_' . $state)),
                Tables\Columns\TextColumn::make('status')->label(__('tasks.status'))->badge()
                    ->color(fn ($state) => match($state) { 'done' => 'success', 'in_progress' => 'warning', default => 'gray' })
                    ->formatStateUsing(fn ($state) => __('tasks.status_' . $state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('tasks.status'))
                    ->options([
                        'todo'        => __('tasks.status_todo'),
                        'in_progress' => __('tasks.status_in_progress'),
                        'done'        => __('tasks.status_done'),
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('tasks.add')),
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->label(__('tasks.mark_done'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'done')
                    ->action(fn ($record) => $record->update(['status' => 'done', 'completed_at' => now()])),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ]);
    }
}
