<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\LegalCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    use \App\Filament\Concerns\OfficeOnlyResource;

    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'المالية';
    protected static ?string $navigationLabel = 'المصروفات';
    protected static ?string $modelLabel = 'مصروف';
    protected static ?string $pluralModelLabel = 'المصروفات';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل المصروف')
                    ->schema([
                        Forms\Components\TextInput::make('title.ar')
                            ->label('الوصف (عربي)')
                            ->required(),
                        Forms\Components\TextInput::make('title.en')
                            ->label('الوصف (إنجليزي)'),
                        Forms\Components\Select::make('case_id')
                            ->label('القضية')
                            ->relationship('legalCase', 'case_number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->case_number . ' — ' . ($record->getTranslation('title', 'ar') ?: $record->getTranslation('title', 'en')))
                            ->preload()
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('category')
                            ->label('الفئة')
                            ->options([
                                'court_fees'    => 'رسوم قضائية',
                                'translation'   => 'ترجمة',
                                'transportation'=> 'مواصلات',
                                'printing'      => 'طباعة',
                                'expertise'     => 'خبرة',
                                'other'         => 'أخرى',
                            ])
                            ->required(),
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
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending'   => 'معلق',
                                'approved'  => 'موافق عليه',
                                'paid'      => 'مدفوع',
                                'rejected'  => 'مرفوض',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('تاريخ الدفع')
                            ->nullable(),
                        Forms\Components\FileUpload::make('receipt_path')
                            ->label('الإيصال')
                            ->disk('public')
                            ->directory('receipts')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(fn () => \App\Models\PlatformSetting::get('media.max_upload_kb', 5120))
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('الوصف')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', 'ar') ?: $record->getTranslation('title', 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('legalCase.case_number')
                    ->label('القضية')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('الفئة')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'court_fees'     => 'رسوم قضائية',
                        'translation'    => 'ترجمة',
                        'transportation' => 'مواصلات',
                        'printing'       => 'طباعة',
                        'expertise'      => 'خبرة',
                        default          => 'أخرى',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money(fn ($record) => $record->currency)
                    ->visible(fn () => \App\Support\FieldAccess::financials())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'pending'  => 'warning',
                        'approved' => 'info',
                        'paid'     => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending'  => 'معلق',
                        'approved' => 'موافق عليه',
                        'paid'     => 'مدفوع',
                        'rejected' => 'مرفوض',
                        default    => $state,
                    }),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->dateTime('Y/m/d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending'  => 'معلق',
                        'approved' => 'موافق عليه',
                        'paid'     => 'مدفوع',
                        'rejected' => 'مرفوض',
                    ]),
                Tables\Filters\SelectFilter::make('currency')
                    ->label('العملة')
                    ->options(['EGP' => 'جنيه', 'SAR' => 'ريال', 'USD' => 'دولار']),
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
            ->with(['legalCase']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit'   => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
