<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTemplateResource\Pages;
use App\Models\DocumentTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentTemplateResource extends Resource
{
    protected static ?string $model = DocumentTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'الوثائق';
    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string        { return 'قالب وثيقة'; }
    public static function getPluralModelLabel(): string  { return 'قوالب الوثائق'; }
    public static function getNavigationLabel(): string   { return 'قوالب الوثائق'; }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()
                ->tabs([
                    Forms\Components\Tabs\Tab::make('المعلومات الأساسية')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name.ar')
                                    ->label('الاسم (عربي)')
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\TextInput::make('name.en')
                                    ->label('الاسم (إنجليزي)')
                                    ->maxLength(200),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('category')
                                    ->label('الفئة')
                                    ->required()
                                    ->options([
                                        'legal'       => 'قانوني',
                                        'financial'   => 'مالي',
                                        'personal'    => 'شخصي',
                                        'court'       => 'محكمة',
                                        'contract'    => 'عقود',
                                        'other'       => 'أخرى',
                                    ]),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('مفعّل')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('محتوى القالب')
                        ->schema([
                            Forms\Components\Textarea::make('content')
                                ->label('محتوى القالب')
                                ->required()
                                ->rows(18)
                                ->columnSpanFull()
                                ->hint('استخدم {{placeholder_key}} لإدراج القيم الديناميكية')
                                ->hintIcon('heroicon-o-information-circle'),
                        ]),

                    Forms\Components\Tabs\Tab::make('المتغيرات (Placeholders)')
                        ->schema([
                            Forms\Components\Repeater::make('placeholders')
                                ->label('المتغيرات')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('key')
                                            ->label('المفتاح')
                                            ->required()
                                            ->placeholder('client_name')
                                            ->helperText('يُستخدم في القالب كـ {{key}}'),
                                        Forms\Components\TextInput::make('label')
                                            ->label('التسمية')
                                            ->required()
                                            ->placeholder('اسم العميل'),
                                    ]),
                                ])
                                ->addActionLabel('إضافة متغير')
                                ->reorderable()
                                ->collapsible()
                                ->columnSpanFull()
                                ->defaultItems(0),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم القالب')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar') ?: $record->getTranslation('name', 'en'))
                    ->searchable(['name'])
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('الفئة')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'legal'     => 'قانوني',
                        'financial' => 'مالي',
                        'personal'  => 'شخصي',
                        'court'     => 'محكمة',
                        'contract'  => 'عقود',
                        default     => 'أخرى',
                    })
                    ->colors([
                        'primary' => 'legal',
                        'success' => 'financial',
                        'warning' => 'court',
                        'info'    => 'contract',
                        'gray'    => 'other',
                    ]),

                Tables\Columns\TextColumn::make('placeholders_count')
                    ->label('عدد المتغيرات')
                    ->getStateUsing(fn ($record) => count($record->placeholders ?? []))
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->date('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('الفئة')
                    ->options([
                        'legal'     => 'قانوني',
                        'financial' => 'مالي',
                        'personal'  => 'شخصي',
                        'court'     => 'محكمة',
                        'contract'  => 'عقود',
                        'other'     => 'أخرى',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->trueLabel('مفعّل')
                    ->falseLabel('معطّل'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('معاينة')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'معاينة: ' . ($record->getTranslation('name', 'ar') ?: $record->getTranslation('name', 'en')))
                    ->modalContent(fn ($record) => view('filament.modals.template-preview', ['template' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('إغلاق'),

                Tables\Actions\Action::make('duplicate')
                    ->label('نسخ')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('نسخ القالب')
                    ->modalDescription('سيتم إنشاء نسخة جديدة من هذا القالب.')
                    ->action(function ($record) {
                        $newTemplate = $record->replicate();
                        $newTemplate->setTranslation('name', 'ar', $record->getTranslation('name', 'ar') . ' (نسخة)');
                        $newTemplate->setTranslation('name', 'en', $record->getTranslation('name', 'en') . ' (Copy)');
                        $newTemplate->is_active = false;
                        $newTemplate->save();
                    })
                    ->successNotificationTitle('تم نسخ القالب بنجاح'),

                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('لا توجد قوالب بعد')
            ->emptyStateDescription('أنشئ أول قالب وثيقة من الزر أعلاه.')
            ->emptyStateIcon('heroicon-o-document-duplicate');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocumentTemplates::route('/'),
            'create' => Pages\CreateDocumentTemplate::route('/create'),
            'edit'   => Pages\EditDocumentTemplate::route('/{record}/edit'),
        ];
    }
}
