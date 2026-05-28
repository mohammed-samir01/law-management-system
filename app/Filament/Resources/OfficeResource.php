<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string { return 'مكتب'; }
    public static function getPluralModelLabel(): string { return 'المكاتب'; }
    public static function getNavigationLabel(): string { return 'المكاتب'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات المكتب')->schema([
                Forms\Components\TextInput::make('name.ar')
                    ->label('الاسم (عربي)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name.en')
                    ->label('الاسم (إنجليزي)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('المعرف الفريد (Slug)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->alphaDash(),
                Forms\Components\FileUpload::make('logo')
                    ->label('الشعار')
                    ->image()
                    ->directory('offices/logos')
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('بيانات التواصل')->schema([
                Forms\Components\TextInput::make('phone')
                    ->label('الهاتف')
                    ->tel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_number')
                    ->label('الرقم الضريبي')
                    ->maxLength(100),
                Forms\Components\Textarea::make('address.ar')
                    ->label('العنوان (عربي)')
                    ->rows(2),
                Forms\Components\Textarea::make('address.en')
                    ->label('العنوان (إنجليزي)')
                    ->rows(2),
            ])->columns(2),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الحالة'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit'   => Pages\EditOffice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}
