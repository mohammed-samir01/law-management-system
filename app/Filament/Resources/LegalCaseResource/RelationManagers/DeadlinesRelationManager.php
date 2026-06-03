<?php

namespace App\Filament\Resources\LegalCaseResource\RelationManagers;

use App\Services\DeadlineCalculatorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DeadlinesRelationManager extends RelationManager
{
    protected static string $relationship = 'deadlines';
    protected static ?string $title       = 'المواعيد القانونية';
    protected static ?string $label        = 'موعد';

    private function typeOptions(): array
    {
        return [
            'appeal'          => __('deadlines.type_appeal'),
            'cassation'       => __('deadlines.type_cassation'),
            'objection'       => __('deadlines.type_objection'),
            'grievance'       => __('deadlines.type_grievance'),
            'reconsideration' => __('deadlines.type_reconsideration'),
            'custom'          => __('deadlines.type_custom'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')->label(__('deadlines.type'))
                ->options($this->typeOptions())->required(),
            Forms\Components\Select::make('jurisdiction')->label(__('deadlines.jurisdiction'))
                ->options(['eg' => 'مصر', 'sa' => 'السعودية'])->nullable(),
            Forms\Components\DatePicker::make('basis_date')->label(__('deadlines.basis_date'))->required(),
            Forms\Components\DatePicker::make('due_date')->label(__('deadlines.due_date'))->required(),
            Forms\Components\TextInput::make('duration_days')->label(__('deadlines.duration_days'))->numeric()->minValue(1)->required(),
            Forms\Components\Select::make('status')->label(__('deadlines.status'))
                ->options([
                    'open'      => __('deadlines.status_open'),
                    'met'       => __('deadlines.status_met'),
                    'lapsed'    => __('deadlines.status_lapsed'),
                    'cancelled' => __('deadlines.status_cancelled'),
                ])->default('open')->required(),
            Forms\Components\TextInput::make('title.ar')->label(__('deadlines.note') . ' (عربي)')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('due_date')
            ->columns([
                Tables\Columns\TextColumn::make('type')->label(__('deadlines.type'))->badge()
                    ->formatStateUsing(fn ($state) => $this->typeOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('basis_date')->label(__('deadlines.basis_date'))->date('Y/m/d'),
                Tables\Columns\TextColumn::make('due_date')->label(__('deadlines.due_date'))->date('Y/m/d')
                    ->color(fn ($record) => $record->isOpen() && $record->due_date?->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('due_date')->label(__('deadlines.days_left'))
                    ->state(fn ($record) => $record->isOpen() ? $record->daysLeft() . ' يوم' : '—'),
                Tables\Columns\TextColumn::make('status')->label(__('deadlines.status'))->badge()
                    ->color(fn ($state) => match($state) { 'met' => 'success', 'lapsed' => 'danger', 'cancelled' => 'gray', default => 'warning' })
                    ->formatStateUsing(fn ($state) => __('deadlines.status_' . $state)),
            ])
            ->headerActions([
                Tables\Actions\Action::make('compute')
                    ->label(__('deadlines.compute'))
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('jurisdiction')->label(__('deadlines.jurisdiction'))
                            ->options(['eg' => 'مصر', 'sa' => 'السعودية'])->default('eg')->required(),
                        Forms\Components\Select::make('type')->label(__('deadlines.type'))
                            ->options($this->typeOptions())->default('appeal')->required(),
                        Forms\Components\DatePicker::make('basis_date')->label(__('deadlines.basis_date'))->default(now())->required(),
                    ])
                    ->action(function (array $data) {
                        app(DeadlineCalculatorService::class)->computeFor(
                            $this->getOwnerRecord(),
                            \Illuminate\Support\Carbon::parse($data['basis_date']),
                            $data['type'],
                            $data['jurisdiction'],
                        );
                        Notification::make()->title(__('deadlines.computed'))->success()->send();
                    }),
                Tables\Actions\CreateAction::make()->label(__('deadlines.add')),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_met')
                    ->label(__('deadlines.mark_met'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->action(fn ($record) => $record->update(['status' => 'met', 'met_at' => now()])),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ]);
    }
}
