<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Office;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string { return 'مستخدم'; }
    public static function getPluralModelLabel(): string { return 'المستخدمون'; }
    public static function getNavigationLabel(): string { return 'المستخدمون'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('المعلومات الأساسية')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم الكامل')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('الهاتف')
                    ->tel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->maxLength(255),
            ])->columns(2),

            Forms\Components\Section::make('الصلاحيات والمكتب')->schema([
                Forms\Components\Select::make('office_id')
                    ->label('المكتب')
                    ->options(
                        Office::withoutGlobalScopes()->get()->mapWithKeys(fn ($o) => [$o->id => $o->getTranslation('name', 'ar') ?: $o->getTranslation('name', 'en')])
                    )
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('roles')
                    ->label('الدور الوظيفي')
                    ->options(Role::pluck('name', 'name'))
                    ->multiple()
                    ->relationship('roles', 'name'),
                Forms\Components\Select::make('language')
                    ->label('اللغة')
                    ->options(['ar' => 'العربية', 'en' => 'English'])
                    ->default('ar'),
                Forms\Components\Select::make('theme')
                    ->label('المظهر')
                    ->options(['light' => 'فاتح', 'dark' => 'داكن'])
                    ->default('light'),
                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make('معلومات إضافية')->schema([
                Forms\Components\FileUpload::make('avatar')
                    ->label('الصورة الشخصية')
                    ->image()
                    ->directory('users/avatars')
                    ->nullable(),
                Forms\Components\Textarea::make('bio')
                    ->label('نبذة تعريفية')
                    ->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('الدور')
                    ->badge(),
                Tables\Columns\TextColumn::make('office.name')
                    ->label('المكتب')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->office?->getTranslation('name', 'ar')),
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
                Tables\Filters\SelectFilter::make('roles')
                    ->label('الدور')
                    ->relationship('roles', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['roles', 'office']);
    }
}
