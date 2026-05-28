<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnforcementFileResource\Pages;
use App\Filament\Resources\EnforcementFileResource\RelationManagers;
use App\Models\EnforcementFile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnforcementFileResource extends Resource
{
    protected static ?string $model = EnforcementFile::class;

    protected static ?string $navigationIcon  = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'القضايا';
    protected static ?string $navigationLabel = 'ملفات التنفيذ';
    protected static ?string $modelLabel      = 'ملف تنفيذ';
    protected static ?string $pluralModelLabel= 'ملفات التنفيذ';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('enforcement.enforcement_file'))
                    ->schema([
                        Forms\Components\TextInput::make('file_number')
                            ->label(__('enforcement.file_number'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'ENF-' . now()->format('Y') . '-' . str_pad(EnforcementFile::withoutGlobalScopes()->count() + 1, 4, '0', STR_PAD_LEFT)),

                        Forms\Components\Select::make('status')
                            ->label(__('enforcement.status'))
                            ->options([
                                'active'    => __('enforcement.statuses.active'),
                                'completed' => __('enforcement.statuses.completed'),
                                'withdrawn' => __('enforcement.statuses.withdrawn'),
                            ])
                            ->default('active')
                            ->required(),

                        Forms\Components\TextInput::make('title.ar')
                            ->label(__('enforcement.title') . ' (عربي)')
                            ->required(),

                        Forms\Components\TextInput::make('title.en')
                            ->label(__('enforcement.title') . ' (English)'),

                        Forms\Components\TextInput::make('enforcement_office')
                            ->label(__('enforcement.enforcement_office'))
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('أطراف التنفيذ')
                    ->schema([
                        Forms\Components\TextInput::make('debtor_name.ar')
                            ->label(__('enforcement.debtor_name') . ' (عربي)')
                            ->required(),

                        Forms\Components\TextInput::make('debtor_name.en')
                            ->label(__('enforcement.debtor_name') . ' (English)'),

                        Forms\Components\TextInput::make('creditor_name.ar')
                            ->label(__('enforcement.creditor_name') . ' (عربي)')
                            ->required(),

                        Forms\Components\TextInput::make('creditor_name.en')
                            ->label(__('enforcement.creditor_name') . ' (English)'),
                    ])->columns(2),

                Forms\Components\Section::make('مبلغ الدين')
                    ->schema([
                        Forms\Components\TextInput::make('debt_amount')
                            ->label(__('enforcement.debt_amount'))
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        Forms\Components\Select::make('currency')
                            ->label(__('enforcement.currency'))
                            ->options([
                                'EGP' => 'جنيه مصري',
                                'SAR' => 'ريال سعودي',
                                'USD' => 'دولار أمريكي',
                            ])
                            ->default('SAR')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('file_number')
                    ->label(__('enforcement.file_number'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('enforcement.title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', 'ar') ?: $record->getTranslation('title', 'en'))
                    ->limit(40),

                Tables\Columns\TextColumn::make('debtor_name')
                    ->label(__('enforcement.debtor_name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('debtor_name', 'ar') ?: $record->getTranslation('debtor_name', 'en'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('debt_amount')
                    ->label(__('enforcement.debt_amount'))
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('enforcement.status'))
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'active'    => 'success',
                        'completed' => 'gray',
                        'withdrawn' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($record) => $record->status_label),

                Tables\Columns\TextColumn::make('stages_count')
                    ->label('المراحل')
                    ->counts('stages')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('enforcement_office')
                    ->label(__('enforcement.enforcement_office'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('enforcement.status'))
                    ->options([
                        'active'    => __('enforcement.statuses.active'),
                        'completed' => __('enforcement.statuses.completed'),
                        'withdrawn' => __('enforcement.statuses.withdrawn'),
                    ]),

                Tables\Filters\SelectFilter::make('currency')
                    ->label(__('enforcement.currency'))
                    ->options(['EGP' => 'جنيه', 'SAR' => 'ريال', 'USD' => 'دولار']),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),

                Tables\Actions\Action::make('complete')
                    ->label('إنهاء التنفيذ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'completed'])),

                Tables\Actions\DeleteAction::make()->label('حذف'),
                Tables\Actions\RestoreAction::make()->label('استعادة'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\RestoreBulkAction::make()->label('استعادة المحدد'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['client', 'legalCase', 'createdBy'])
            ->withCount('stages');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StagesRelationManager::class,
            RelationManagers\PowersOfAttorneyRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEnforcementFiles::route('/'),
            'create' => Pages\CreateEnforcementFile::route('/create'),
            'view'   => Pages\ViewEnforcementFile::route('/{record}'),
            'edit'   => Pages\EditEnforcementFile::route('/{record}/edit'),
        ];
    }
}
