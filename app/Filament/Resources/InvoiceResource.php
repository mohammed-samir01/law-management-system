<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\PDFService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'المالية';
    protected static ?string $navigationLabel = 'الفواتير';
    protected static ?string $modelLabel = 'فاتورة';
    protected static ?string $pluralModelLabel = 'الفواتير';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الفاتورة')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('رقم الفاتورة')
                            ->default(fn () => 'INV-'.str_pad(Invoice::withoutGlobalScopes()->count() + 1, 5, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('case_id')
                            ->label('القضية')
                            ->relationship('legalCase', 'case_number')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft'     => 'مسودة',
                                'sent'      => 'مرسلة',
                                'paid'      => 'مدفوعة',
                                'overdue'   => 'متأخرة',
                                'cancelled' => 'ملغاة',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->nullable(),
                        Forms\Components\Select::make('currency')
                            ->label('العملة')
                            ->options([
                                'EGP' => 'جنيه مصري',
                                'SAR' => 'ريال سعودي',
                                'USD' => 'دولار أمريكي',
                            ])
                            ->default('EGP')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('المبالغ')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ الأساسي')
                            ->numeric()
                            ->minValue(0)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $amount = (float) ($get('amount') ?? 0);
                                $tax    = (float) ($get('tax_amount') ?? 0);
                                $set('total_amount', $amount + $tax);
                            })
                            ->required(),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('الضريبة')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $amount = (float) ($get('amount') ?? 0);
                                $tax    = (float) ($get('tax_amount') ?? 0);
                                $set('total_amount', $amount + $tax);
                            }),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('الإجمالي')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('ملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('notes.ar')
                            ->label('ملاحظات (عربي)')
                            ->nullable(),
                        Forms\Components\Textarea::make('notes.en')
                            ->label('ملاحظات (إنجليزي)')
                            ->nullable(),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('legalCase.case_number')
                    ->label('القضية')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'draft'     => 'gray',
                        'sent'      => 'info',
                        'paid'      => 'success',
                        'overdue'   => 'danger',
                        'cancelled' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($record) => $record->status_label),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('تاريخ الاستحقاق')
                    ->date('Y/m/d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft'     => 'مسودة',
                        'sent'      => 'مرسلة',
                        'paid'      => 'مدفوعة',
                        'overdue'   => 'متأخرة',
                        'cancelled' => 'ملغاة',
                    ]),
                Tables\Filters\SelectFilter::make('currency')
                    ->label('العملة')
                    ->options(['EGP' => 'جنيه', 'SAR' => 'ريال', 'USD' => 'دولار']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label('تحميل PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function ($record) {
                        $path = app(PDFService::class)->generateInvoicePDF($record);
                        return response()->download(storage_path('app/public/'.$path));
                    }),
                Tables\Actions\Action::make('mark_paid')
                    ->label('تحديد كمدفوعة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'paid')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'paid'])),
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
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
