<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentGatewayResource\Pages;
use App\Models\PaymentGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentGatewayResource extends Resource
{
    protected static ?string $model = PaymentGateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'بوابات الدفع';
    protected static ?string $modelLabel = 'بوابة دفع';
    protected static ?string $pluralModelLabel = 'بوابات الدفع';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات البوابة')
                    ->schema([
                        Forms\Components\Select::make('gateway_name')
                            ->label('اسم البوابة')
                            ->options([
                                'paymob'        => 'Paymob (مصر)',
                                'instapay'      => 'InstaPay (مصر)',
                                'vodafone_cash' => 'Vodafone Cash (مصر)',
                                'moyasar'       => 'Moyasar (السعودية)',
                                'mada'          => 'Mada (السعودية)',
                                'paytabs'       => 'PayTabs (السعودية)',
                                'stripe'        => 'Stripe (عالمي)',
                                'paypal'        => 'PayPal (عالمي)',
                                'bank_transfer' => 'تحويل بنكي',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('display_name.ar')
                            ->label('الاسم المعروض (عربي)')
                            ->required(),
                        Forms\Components\TextInput::make('display_name.en')
                            ->label('الاسم المعروض (إنجليزي)'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعّل')
                            ->default(true),
                        Forms\Components\Toggle::make('test_mode')
                            ->label('وضع الاختبار')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('إعدادات API')
                    ->description('يتم تخزين هذه البيانات مشفرة بالكامل.')
                    ->schema([
                        Forms\Components\KeyValue::make('config_fields')
                            ->label('مفاتيح الإعداد')
                            ->keyLabel('المفتاح')
                            ->valueLabel('القيمة')
                            ->addButtonLabel('إضافة مفتاح')
                            ->helperText('أدخل مفاتيح API الخاصة بالبوابة. مثال: api_key, secret_key, merchant_id')
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gateway_name')
                    ->label('البوابة')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'paymob'        => 'Paymob',
                        'instapay'      => 'InstaPay',
                        'vodafone_cash' => 'Vodafone Cash',
                        'moyasar'       => 'Moyasar',
                        'mada'          => 'Mada',
                        'paytabs'       => 'PayTabs',
                        'stripe'        => 'Stripe',
                        'paypal'        => 'PayPal',
                        'bank_transfer' => 'تحويل بنكي',
                        default         => $state,
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('الاسم المعروض')
                    ->getStateUsing(fn ($record) => $record->getTranslation('display_name', 'ar') ?: $record->getTranslation('display_name', 'en')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّل')
                    ->boolean(),
                Tables\Columns\IconColumn::make('test_mode')
                    ->label('اختبار')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->trueLabel('مفعّل')
                    ->falseLabel('معطّل'),
                Tables\Filters\TernaryFilter::make('test_mode')
                    ->label('وضع الاختبار')
                    ->trueLabel('اختبار')
                    ->falseLabel('حقيقي'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPaymentGateways::route('/'),
            'create' => Pages\CreatePaymentGateway::route('/create'),
            'edit'   => Pages\EditPaymentGateway::route('/{record}/edit'),
        ];
    }
}
