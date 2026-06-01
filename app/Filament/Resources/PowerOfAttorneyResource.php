<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PowerOfAttorneyResource\Pages;
use App\Models\LegalCase;
use App\Models\EnforcementFile;
use App\Models\PowerOfAttorney;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PowerOfAttorneyResource extends Resource
{
    use \App\Filament\Concerns\OfficeOnlyResource;

    protected static ?string $model = PowerOfAttorney::class;

    protected static ?string $navigationIcon  = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'القضايا';
    protected static ?string $navigationLabel = 'التوكيلات';
    protected static ?string $modelLabel      = 'توكيل';
    protected static ?string $pluralModelLabel= 'التوكيلات';
    protected static ?int    $navigationSort  = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('enforcement.poa_single'))
                ->schema([
                    Forms\Components\TextInput::make('poa_number')
                        ->label(__('enforcement.poa_number'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->default(fn () => 'POA-' . now()->format('Y') . '-' . str_pad(PowerOfAttorney::withoutGlobalScopes()->count() + 1, 4, '0', STR_PAD_LEFT)),

                    Forms\Components\Select::make('type')
                        ->label(__('enforcement.poa_type'))
                        ->options([
                            'general'  => 'عام',
                            'special'  => 'خاص',
                            'judicial' => 'قضائي',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('representative_name.ar')
                        ->label(__('enforcement.representative') . ' (عربي)')
                        ->required(),

                    Forms\Components\TextInput::make('representative_name.en')
                        ->label(__('enforcement.representative') . ' (English)'),

                    Forms\Components\DatePicker::make('valid_from')
                        ->label(__('enforcement.valid_from')),

                    Forms\Components\DatePicker::make('valid_until')
                        ->label(__('enforcement.valid_until')),

                    Forms\Components\Select::make('status')
                        ->label(__('enforcement.status'))
                        ->options([
                            'active'  => __('enforcement.poa_statuses.active'),
                            'expired' => __('enforcement.poa_statuses.expired'),
                            'revoked' => __('enforcement.poa_statuses.revoked'),
                        ])
                        ->default('active')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('الارتباط')
                ->schema([
                    Forms\Components\Select::make('case_id')
                        ->label(__('enforcement.poa_single') . ' — القضية')
                        ->options(LegalCase::pluck('case_number', 'id'))
                        ->searchable()
                        ->nullable(),

                    Forms\Components\Select::make('enforcement_file_id')
                        ->label('ملف التنفيذ')
                        ->options(EnforcementFile::pluck('file_number', 'id'))
                        ->searchable()
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('الصلاحيات')
                ->schema([
                    Forms\Components\Textarea::make('authorities.ar')
                        ->label('الصلاحيات (عربي)')
                        ->rows(3),

                    Forms\Components\Textarea::make('authorities.en')
                        ->label('الصلاحيات (English)')
                        ->rows(3),
                ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('poa_number')
                    ->label(__('enforcement.poa_number'))
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('representative_name')
                    ->label(__('enforcement.representative'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('representative_name', 'ar') ?: $record->getTranslation('representative_name', 'en'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('enforcement.poa_type'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        'general'  => 'عام',
                        'special'  => 'خاص',
                        'judicial' => 'قضائي',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('valid_from')
                    ->label(__('enforcement.valid_from'))
                    ->date('Y/m/d'),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label(__('enforcement.valid_until'))
                    ->date('Y/m/d')
                    ->color(function ($state) {
                        if (! $state) return null;
                        $date = $state instanceof \Carbon\Carbon ? $state : \Carbon\Carbon::parse($state);
                        return $date->isPast() ? 'danger' : null;
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('enforcement.status'))
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'active'  => 'success',
                        'expired' => 'warning',
                        'revoked' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active'  => __('enforcement.poa_statuses.active'),
                        'expired' => __('enforcement.poa_statuses.expired'),
                        'revoked' => __('enforcement.poa_statuses.revoked'),
                        default   => $state,
                    }),

                Tables\Columns\TextColumn::make('legalCase.case_number')
                    ->label('القضية')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('enforcementFile.file_number')
                    ->label('ملف التنفيذ')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('enforcement.status'))
                    ->options([
                        'active'  => __('enforcement.poa_statuses.active'),
                        'expired' => __('enforcement.poa_statuses.expired'),
                        'revoked' => __('enforcement.poa_statuses.revoked'),
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('enforcement.poa_type'))
                    ->options(['general' => 'عام', 'special' => 'خاص', 'judicial' => 'قضائي']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),

                Tables\Actions\Action::make('revoke')
                    ->label('إلغاء التوكيل')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'revoked'])),

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
            ->with(['legalCase', 'enforcementFile']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPowersOfAttorney::route('/'),
            'create' => Pages\CreatePowerOfAttorney::route('/create'),
            'edit'   => Pages\EditPowerOfAttorney::route('/{record}/edit'),
        ];
    }
}
