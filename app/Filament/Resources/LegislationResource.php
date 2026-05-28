<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegislationResource\Pages;
use App\Models\Legislation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LegislationResource extends Resource
{
    protected static ?string $model = Legislation::class;

    protected static ?string $navigationIcon  = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'القضايا';
    protected static ?string $navigationLabel = 'التشريعات';
    protected static ?string $modelLabel      = 'تشريع';
    protected static ?string $pluralModelLabel= 'التشريعات';
    protected static ?int    $navigationSort  = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('legislation.legislation'))
                ->schema([
                    Forms\Components\TextInput::make('law_number')
                        ->label(__('legislation.law_number'))
                        ->required(),

                    Forms\Components\Select::make('type')
                        ->label(__('legislation.type'))
                        ->options([
                            'law'        => __('legislation.types.law'),
                            'decree'     => __('legislation.types.decree'),
                            'regulation' => __('legislation.types.regulation'),
                            'circular'   => __('legislation.types.circular'),
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('name.ar')
                        ->label(__('legislation.name') . ' (عربي)')
                        ->required(),

                    Forms\Components\TextInput::make('name.en')
                        ->label(__('legislation.name') . ' (English)'),

                    Forms\Components\DatePicker::make('enactment_date')
                        ->label(__('legislation.enactment_date')),

                    Forms\Components\Select::make('status')
                        ->label(__('legislation.status'))
                        ->options([
                            'active'   => __('legislation.statuses.active'),
                            'amended'  => __('legislation.statuses.amended'),
                            'repealed' => __('legislation.statuses.repealed'),
                        ])
                        ->default('active')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('الوصف')
                ->schema([
                    Forms\Components\Textarea::make('description.ar')
                        ->label('الوصف (عربي)')
                        ->rows(4),

                    Forms\Components\Textarea::make('description.en')
                        ->label('Description (English)')
                        ->rows(4),
                ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('enactment_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('law_number')
                    ->label(__('legislation.law_number'))
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('legislation.name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar') ?: $record->getTranslation('name', 'en'))
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('legislation.type'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        'law'        => __('legislation.types.law'),
                        'decree'     => __('legislation.types.decree'),
                        'regulation' => __('legislation.types.regulation'),
                        'circular'   => __('legislation.types.circular'),
                        default      => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'law'        => 'primary',
                        'decree'     => 'info',
                        'regulation' => 'warning',
                        'circular'   => 'gray',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('enactment_date')
                    ->label(__('legislation.enactment_date'))
                    ->date('Y/m/d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('legislation.status'))
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'active'   => 'success',
                        'amended'  => 'warning',
                        'repealed' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active'   => __('legislation.statuses.active'),
                        'amended'  => __('legislation.statuses.amended'),
                        'repealed' => __('legislation.statuses.repealed'),
                        default    => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('legislation.type'))
                    ->options([
                        'law'        => __('legislation.types.law'),
                        'decree'     => __('legislation.types.decree'),
                        'regulation' => __('legislation.types.regulation'),
                        'circular'   => __('legislation.types.circular'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('legislation.status'))
                    ->options([
                        'active'   => __('legislation.statuses.active'),
                        'amended'  => __('legislation.statuses.amended'),
                        'repealed' => __('legislation.statuses.repealed'),
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
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
            ->with(['creator']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLegislations::route('/'),
            'create' => Pages\CreateLegislation::route('/create'),
            'edit'   => Pages\EditLegislation::route('/{record}/edit'),
        ];
    }
}
