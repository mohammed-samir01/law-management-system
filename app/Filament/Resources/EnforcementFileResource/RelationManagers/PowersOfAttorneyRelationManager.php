<?php

namespace App\Filament\Resources\EnforcementFileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PowersOfAttorneyRelationManager extends RelationManager
{
    protected static string $relationship = 'powersOfAttorney';
    protected static ?string $title       = 'التوكيلات المرتبطة';
    protected static ?string $label       = 'توكيل';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('poa_number')
                ->label(__('enforcement.poa_number'))
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('representative_name.ar')
                ->label(__('enforcement.representative') . ' (عربي)')
                ->required(),

            Forms\Components\TextInput::make('representative_name.en')
                ->label(__('enforcement.representative') . ' (English)'),

            Forms\Components\Select::make('type')
                ->label(__('enforcement.poa_type'))
                ->options([
                    'general'  => 'عام',
                    'special'  => 'خاص',
                    'judicial' => 'قضائي',
                ])
                ->required(),

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

            Forms\Components\Textarea::make('authorities.ar')
                ->label('الصلاحيات (عربي)')
                ->nullable()
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('poa_number')
                    ->label(__('enforcement.poa_number'))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('representative_name')
                    ->label(__('enforcement.representative'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('representative_name', 'ar')),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('enforcement.poa_type'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        'general'  => 'عام',
                        'special'  => 'خاص',
                        'judicial' => 'قضائي',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label(__('enforcement.valid_until'))
                    ->date('Y/m/d'),

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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة توكيل')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['office_id']  = auth()->user()->office_id;
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([]);
    }
}
