<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    use \App\Filament\Concerns\OfficeOnlyResource;

    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'المالية';
    protected static ?string $navigationLabel = 'المدفوعات';
    protected static ?string $modelLabel = 'دفعة';
    protected static ?string $pluralModelLabel = 'المدفوعات';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الدفعة')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'ar') ?: $record->getTranslation('name', 'en'))
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('case_id')
                            ->label('القضية')
                            ->relationship('legalCase', 'case_number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->case_number . ' — ' . ($record->getTranslation('title', 'ar') ?: $record->getTranslation('title', 'en')))
                            ->preload()
                            ->searchable()
                            ->nullable(),
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->label('العملة')
                            ->options([
                                'EGP' => 'جنيه مصري',
                                'SAR' => 'ريال سعودي',
                                'USD' => 'دولار أمريكي',
                            ])
                            ->default('EGP')
                            ->required(),
                        Forms\Components\Select::make('method')
                            ->label('طريقة الدفع')
                            ->options([
                                'cash'           => 'نقدي',
                                'bank_transfer'  => 'تحويل بنكي',
                                'card'           => 'بطاقة',
                                'paymob'         => 'Paymob',
                                'instapay'       => 'InstaPay',
                                'vodafone_cash'  => 'Vodafone Cash',
                                'moyasar'        => 'Moyasar',
                                'stripe'         => 'Stripe',
                                'paypal'         => 'PayPal',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('gateway')
                            ->label('البوابة')
                            ->nullable()
                            ->hidden(),
                        Forms\Components\TextInput::make('gateway_transaction_id')
                            ->label('رقم المعاملة')
                            ->nullable(),
                        Forms\Components\TextInput::make('reference')
                            ->label('المرجع')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending'   => 'معلق',
                                'completed' => 'مكتمل',
                                'failed'    => 'فاشل',
                                'refunded'  => 'مُسترد',
                            ])
                            ->default('pending')
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $state === 'completed' ? $set('paid_at', now()->toDateTimeString()) : null
                            )
                            ->required(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('تاريخ الدفع')
                            ->nullable(),
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('legalCase.case_number')
                    ->label('القضية')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money(fn ($record) => $record->currency)
                    ->visible(fn () => \App\Support\FieldAccess::financials())
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cash'          => 'نقدي',
                        'bank_transfer' => 'تحويل بنكي',
                        'card'          => 'بطاقة',
                        default         => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'pending'   => 'warning',
                        'completed' => 'success',
                        'failed'    => 'danger',
                        'refunded'  => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($record) => $record->status_label),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->dateTime('Y/m/d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('المرجع')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending'   => 'معلق',
                        'completed' => 'مكتمل',
                        'failed'    => 'فاشل',
                        'refunded'  => 'مُسترد',
                    ]),
                Tables\Filters\SelectFilter::make('method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash'          => 'نقدي',
                        'bank_transfer' => 'تحويل بنكي',
                        'card'          => 'بطاقة',
                        'paymob'        => 'Paymob',
                        'instapay'      => 'InstaPay',
                        'moyasar'       => 'Moyasar',
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
            ->with(['client', 'legalCase']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
