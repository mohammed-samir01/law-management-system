<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CaseLawResource\Pages;
use App\Models\CaseLaw;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CaseLawResource extends Resource
{
    protected static ?string $model = CaseLaw::class;

    protected static ?string $navigationIcon  = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationGroup = 'القضايا';
    protected static ?string $navigationLabel = 'السوابق القضائية';
    protected static ?string $modelLabel      = 'سابقة قضائية';
    protected static ?string $pluralModelLabel= 'السوابق القضائية';
    protected static ?int    $navigationSort  = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('legislation.case_law'))
                ->schema([
                    Forms\Components\Select::make('court_type')
                        ->label(__('legislation.court_type'))
                        ->options([
                            'supreme'       => __('legislation.court_types.supreme'),
                            'appeal'        => __('legislation.court_types.appeal'),
                            'first_instance'=> __('legislation.court_types.first_instance'),
                            'administrative'=> __('legislation.court_types.administrative'),
                            'constitutional'=> __('legislation.court_types.constitutional'),
                        ])
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('chamber')
                        ->label(__('legislation.chamber'))
                        ->nullable(),

                    Forms\Components\DatePicker::make('decision_date')
                        ->label(__('legislation.decision_date'))
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label(__('legislation.status'))
                        ->options([
                            'published'   => __('legislation.case_law_statuses.published'),
                            'draft'       => __('legislation.case_law_statuses.draft'),
                            'archived'    => __('legislation.case_law_statuses.archived'),
                        ])
                        ->default('published')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('ملخص الحكم')
                ->schema([
                    Forms\Components\Textarea::make('summary.ar')
                        ->label('الملخص (عربي)')
                        ->rows(5)
                        ->required(),

                    Forms\Components\Textarea::make('summary.en')
                        ->label('Summary (English)')
                        ->rows(5),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('decision_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('court_type')
                    ->label(__('legislation.court_type'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        'supreme'        => __('legislation.court_types.supreme'),
                        'appeal'         => __('legislation.court_types.appeal'),
                        'first_instance' => __('legislation.court_types.first_instance'),
                        'administrative' => __('legislation.court_types.administrative'),
                        'constitutional' => __('legislation.court_types.constitutional'),
                        default          => $state,
                    })
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('chamber')
                    ->label(__('legislation.chamber'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('decision_date')
                    ->label(__('legislation.decision_date'))
                    ->date('Y/m/d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('summary')
                    ->label('الملخص')
                    ->getStateUsing(fn ($record) => $record->getTranslation('summary', 'ar') ?: $record->getTranslation('summary', 'en'))
                    ->limit(80)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('legislation.status'))
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        'archived'  => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'published' => __('legislation.case_law_statuses.published'),
                        'draft'     => __('legislation.case_law_statuses.draft'),
                        'archived'  => __('legislation.case_law_statuses.archived'),
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('أضافه')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('court_type')
                    ->label(__('legislation.court_type'))
                    ->options([
                        'supreme'        => __('legislation.court_types.supreme'),
                        'appeal'         => __('legislation.court_types.appeal'),
                        'first_instance' => __('legislation.court_types.first_instance'),
                        'administrative' => __('legislation.court_types.administrative'),
                        'constitutional' => __('legislation.court_types.constitutional'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('legislation.status'))
                    ->options([
                        'published' => __('legislation.case_law_statuses.published'),
                        'draft'     => __('legislation.case_law_statuses.draft'),
                        'archived'  => __('legislation.case_law_statuses.archived'),
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
            'index'  => Pages\ListCaseLaws::route('/'),
            'create' => Pages\CreateCaseLaw::route('/create'),
            'edit'   => Pages\EditCaseLaw::route('/{record}/edit'),
        ];
    }
}
