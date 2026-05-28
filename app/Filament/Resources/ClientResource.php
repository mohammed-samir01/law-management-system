<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'العملاء';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string { return 'عميل'; }
    public static function getPluralModelLabel(): string { return 'العملاء'; }
    public static function getNavigationLabel(): string { return 'العملاء'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات العميل')->schema([
                Forms\Components\Select::make('type')
                    ->label('نوع العميل')
                    ->options(['individual' => 'فرد', 'company' => 'شركة'])
                    ->default('individual')
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('name.ar')
                    ->label('الاسم (عربي)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name.en')
                    ->label('الاسم (إنجليزي)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('id_number')
                    ->label(fn (Forms\Get $get) => $get('type') === 'company' ? 'رقم السجل التجاري' : 'رقم الهوية')
                    ->maxLength(50),
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
                Forms\Components\Textarea::make('address.ar')
                    ->label('العنوان (عربي)')
                    ->rows(2),
                Forms\Components\Textarea::make('address.en')
                    ->label('العنوان (إنجليزي)')
                    ->rows(2),
            ])->columns(2),

            Forms\Components\Section::make('ملاحظات')->schema([
                Forms\Components\Textarea::make('notes.ar')
                    ->label('ملاحظات (عربي)')
                    ->rows(3),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar') ?: $record->getTranslation('name', 'en'))
                    ->searchable(query: fn ($query, $search) => $query->whereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$search}%"]))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn ($state) => $state === 'company' ? 'شركة' : 'فرد')
                    ->colors(['primary' => 'company', 'success' => 'individual']),
                Tables\Columns\TextColumn::make('id_number')
                    ->label('رقم الهوية')
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
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options(['individual' => 'فرد', 'company' => 'شركة']),
                Tables\Filters\TernaryFilter::make('is_active')->label('الحالة'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index'  => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view'   => Pages\ViewClient::route('/{record}'),
            'edit'   => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([\Illuminate\Database\Eloquent\SoftDeletingScope::class])
            ->with(['user']);
    }
}
