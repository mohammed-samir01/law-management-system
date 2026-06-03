<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddonResource\Pages;
use App\Models\Addon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AddonResource extends Resource
{
    protected static ?string $model = Addon::class;
    protected static ?string $navigationIcon  = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?int    $navigationSort  = 6;

    public static function getModelLabel(): string        { return 'إضافة'; }
    public static function getPluralModelLabel(): string  { return 'الإضافات'; }
    public static function getNavigationLabel(): string   { return 'الإضافات'; }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات الإضافة')->schema([
                Forms\Components\TextInput::make('name.ar')
                    ->label('الاسم (عربي)')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('name.en')
                    ->label('الاسم (English)')
                    ->maxLength(100),
                Forms\Components\TextInput::make('slug')
                    ->label('المعرف الفريد')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->helperText('أحرف إنجليزية وأرقام وشرطات — مثال: whatsapp')
                    ->maxLength(60),
                Forms\Components\Textarea::make('description.ar')
                    ->label('الوصف (عربي)')
                    ->rows(3),
                Forms\Components\Textarea::make('description.en')
                    ->label('الوصف (English)')
                    ->rows(3),
                Forms\Components\TextInput::make('icon')
                    ->label('الأيقونة (Heroicon)')
                    ->helperText('مثال: heroicon-o-puzzle-piece')
                    ->default('heroicon-o-puzzle-piece'),
                Forms\Components\Select::make('category')
                    ->label('الفئة')
                    ->options([
                        'communication' => 'التواصل',
                        'legal'         => 'قانونية',
                        'ai'            => 'ذكاء اصطناعي',
                        'client'        => 'العملاء',
                        'analytics'     => 'تحليلات',
                        'general'       => 'عام',
                    ])
                    ->required()
                    ->default('general'),
            ])->columns(2),

            Forms\Components\Section::make('التسعير')->schema([
                Forms\Components\TextInput::make('price_monthly')
                    ->label('السعر الشهري')
                    ->numeric()
                    ->suffix('ج.م')
                    ->default(0),
                Forms\Components\TextInput::make('price_yearly')
                    ->label('السعر السنوي')
                    ->numeric()
                    ->suffix('ج.م')
                    ->default(0),
                Forms\Components\Select::make('currency')
                    ->label('العملة')
                    ->options(['EGP' => 'جنيه مصري', 'SAR' => 'ريال سعودي', 'USD' => 'دولار'])
                    ->default('EGP'),
            ])->columns(3),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('نشطة')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar'))
                    ->searchable(query: fn ($query, $search) =>
                        $query->where('name->ar', 'like', "%{$search}%")
                    ),
                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('category')
                    ->label('الفئة')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'communication' => 'التواصل',
                        'legal'         => 'قانونية',
                        'ai'            => 'ذكاء اصطناعي',
                        'client'        => 'العملاء',
                        'analytics'     => 'تحليلات',
                        default         => 'عام',
                    }),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->label('شهري')
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('price_yearly')
                    ->label('سنوي')
                    ->money('EGP'),
                Tables\Columns\TextColumn::make('office_addons_count')
                    ->label('مكاتب نشطة')
                    ->counts('officeAddons')
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشطة')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAddons::route('/'),
            'create' => Pages\CreateAddon::route('/create'),
            'edit'   => Pages\EditAddon::route('/{record}/edit'),
        ];
    }
}
