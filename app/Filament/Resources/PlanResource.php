<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?int    $navigationSort  = 4;

    public static function getModelLabel(): string       { return 'خطة'; }
    public static function getPluralModelLabel(): string  { return 'الخطط'; }
    public static function getNavigationLabel(): string   { return 'خطط الاشتراك'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات الخطة')->schema([
                Forms\Components\TextInput::make('name.ar')->label('الاسم (عربي)')->required()->maxLength(255),
                Forms\Components\TextInput::make('name.en')->label('الاسم (إنجليزي)')->maxLength(255),
                Forms\Components\TextInput::make('slug')->label('المعرّف (Slug)')->required()->unique(ignoreRecord: true)->alphaDash(),
                Forms\Components\Select::make('currency')->label('العملة')->options(['EGP' => 'جنيه مصري', 'SAR' => 'ريال سعودي', 'USD' => 'دولار أمريكي'])->default('EGP')->required(),
            ])->columns(2),

            Forms\Components\Section::make('الأسعار')->schema([
                Forms\Components\TextInput::make('price_monthly')->label('السعر الشهري')->numeric()->minValue(0)->default(0)->required(),
                Forms\Components\TextInput::make('price_yearly')->label('السعر السنوي')->numeric()->minValue(0)->default(0)->required(),
            ])->columns(2),

            Forms\Components\Section::make('الحدود والمميزات')->schema([
                Forms\Components\TextInput::make('max_users')->label('عدد المستخدمين')->numeric()->minValue(1)->default(5)->required(),
                Forms\Components\TextInput::make('max_cases')->label('عدد القضايا')->numeric()->minValue(1)->default(50)->required(),
                Forms\Components\TextInput::make('max_storage_mb')->label('التخزين (ميجابايت)')->numeric()->minValue(0)->default(1024)->required(),
                Forms\Components\Toggle::make('ai_enabled')->label('الذكاء الاصطناعي مفعّل')->default(false)->live(),
                Forms\Components\TextInput::make('max_ai_requests_monthly')
                    ->label('حد طلبات الذكاء الاصطناعي شهرياً')
                    ->numeric()->minValue(0)
                    ->helperText('اتركه فارغاً = غير محدود')
                    ->visible(fn (Forms\Get $get) => $get('ai_enabled')),
                Forms\Components\TextInput::make('max_ai_tokens_monthly')
                    ->label('حد التوكنز شهرياً')
                    ->numeric()->minValue(0)
                    ->helperText('اتركه فارغاً = غير محدود')
                    ->visible(fn (Forms\Get $get) => $get('ai_enabled')),
                Forms\Components\Toggle::make('custom_branding')->label('علامة تجارية مخصصة')->default(false),
                Forms\Components\Toggle::make('is_active')->label('الخطة مفعّلة')->default(true),
                Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
            ])->columns(2),

            Forms\Components\Section::make('مميزات العرض (تظهر في صفحة الأسعار)')->schema([
                Forms\Components\Repeater::make('features')
                    ->label('المميزات')
                    ->schema([
                        Forms\Components\TextInput::make('ar')
                            ->label('الميزة (عربي)')
                            ->required(),
                        Forms\Components\TextInput::make('en')
                            ->label('Feature (English)')
                            ->required()
                            ->extraInputAttributes(['dir' => 'ltr']),
                    ])
                    ->columns(2)
                    ->addActionLabel('إضافة ميزة')
                    ->default([]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar'))
                    ->searchable(query: fn ($query, $search) => $query->where('name->ar', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('slug')->label('المعرّف'),
                Tables\Columns\TextColumn::make('price_monthly')->label('شهري')->money(fn ($record) => $record->currency),
                Tables\Columns\TextColumn::make('price_yearly')->label('سنوي')->money(fn ($record) => $record->currency),
                Tables\Columns\TextColumn::make('subscriptions_count')->label('المشتركون')->counts('subscriptions')->badge()->color('info'),
                Tables\Columns\IconColumn::make('ai_enabled')->label('AI')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('مفعّلة')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit'   => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
