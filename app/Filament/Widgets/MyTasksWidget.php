<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyTasksWidget extends BaseWidget
{
    protected static ?string $heading = 'مهامي';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Hidden for super_admin (platform owner) and clients.
        $user = auth()->user();
        return $user !== null
            && $user->office_id
            && ! $user->hasRole('super_admin');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query() // office global scope applies automatically
                    ->where('assigned_to', auth()->id())
                    ->where('status', '!=', 'done')
                    ->with('legalCase')
                    ->orderByRaw('due_date is null, due_date asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('tasks.title'))->wrap(),
                Tables\Columns\TextColumn::make('legalCase.case_number')->label(__('tasks.case'))->default('—'),
                Tables\Columns\TextColumn::make('due_date')->label(__('tasks.due_date'))
                    ->date('Y/m/d')->default('—')
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('priority')->label(__('tasks.priority'))->badge()
                    ->color(fn ($state) => match($state) { 'high' => 'danger', 'medium' => 'warning', default => 'gray' })
                    ->formatStateUsing(fn ($state) => __('tasks.priority_' . $state)),
            ])
            ->emptyStateHeading(__('tasks.empty'));
    }
}
