<?php

namespace App\Filament\Resources\SupportTicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';
    protected static ?string $title       = 'الردود';
    protected static ?string $label       = 'رد';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                    ->label('الرد')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('بواسطة')
                    ->icon('heroicon-m-user-circle')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('content')
                    ->label('الرد')
                    ->wrap()
                    ->limit(200),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة رد')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    })
                    ->after(function () {
                        $ticket = $this->getOwnerRecord();
                        if ($ticket->status === 'open') {
                            $ticket->update(['status' => 'in_progress']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->visible(fn ($record) => $record->user_id === auth()->id()),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->visible(fn ($record) => $record->user_id === auth()->id()),
            ])
            ->bulkActions([])
            ->paginated(false);
    }
}
