<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Jobs\AIProcessJob;
use App\Models\Document;
use App\Models\LegalCase;
use App\Models\Hearing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'الوثائق';
    protected static ?string $navigationLabel = 'الوثائق';
    protected static ?string $modelLabel = 'وثيقة';
    protected static ?string $pluralModelLabel = 'الوثائق';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الوثيقة')
                    ->schema([
                        Forms\Components\TextInput::make('title.ar')
                            ->label('العنوان (عربي)')
                            ->required(),
                        Forms\Components\TextInput::make('title.en')
                            ->label('العنوان (إنجليزي)'),
                        Forms\Components\Select::make('type')
                            ->label('النوع')
                            ->options([
                                'contract'   => 'عقد',
                                'pleading'   => 'مذكرة',
                                'verdict'    => 'حكم',
                                'power_of_attorney' => 'توكيل',
                                'evidence'   => 'دليل',
                                'other'      => 'أخرى',
                            ])
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->label('الفئة')
                            ->options([
                                'legal'      => 'قانوني',
                                'financial'  => 'مالي',
                                'personal'   => 'شخصي',
                                'court'      => 'محكمة',
                                'other'      => 'أخرى',
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft'     => 'مسودة',
                                'final'     => 'نهائي',
                                'archived'  => 'مؤرشف',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\TextInput::make('version')
                            ->label('الإصدار')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('المرتبط بـ')
                    ->schema([
                        Forms\Components\Select::make('documentable_type')
                            ->label('نوع الارتباط')
                            ->options([
                                LegalCase::class => 'قضية',
                                Hearing::class   => 'جلسة',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('documentable_id', null)),
                        Forms\Components\Select::make('documentable_id')
                            ->label('اختر القضية / الجلسة')
                            ->options(function (Forms\Get $get) {
                                $type = $get('documentable_type');
                                if ($type === LegalCase::class) {
                                    return LegalCase::pluck('case_number', 'id');
                                }
                                if ($type === Hearing::class) {
                                    return Hearing::with('legalCase')
                                        ->get()
                                        ->mapWithKeys(fn ($h) => [
                                            $h->id => ($h->legalCase?->case_number ?? '-').' — '.$h->scheduled_at?->format('Y/m/d'),
                                        ]);
                                }
                                return [];
                            })
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('الملف')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('files')
                            ->label('رفع الملف')
                            ->collection('files')
                            ->multiple()
                            ->maxSize(20480)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', 'ar') ?: $record->getTranslation('title', 'en'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'contract'          => 'عقد',
                        'pleading'          => 'مذكرة',
                        'verdict'           => 'حكم',
                        'power_of_attorney' => 'توكيل',
                        'evidence'          => 'دليل',
                        default             => 'أخرى',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'draft'    => 'gray',
                        'final'    => 'success',
                        'archived' => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'draft'    => 'مسودة',
                        'final'    => 'نهائي',
                        'archived' => 'مؤرشف',
                        default    => $state,
                    }),
                Tables\Columns\TextColumn::make('version')
                    ->label('الإصدار')
                    ->sortable(),
                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('رُفع بواسطة')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'contract'          => 'عقد',
                        'pleading'          => 'مذكرة',
                        'verdict'           => 'حكم',
                        'power_of_attorney' => 'توكيل',
                        'evidence'          => 'دليل',
                        'other'             => 'أخرى',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft'    => 'مسودة',
                        'final'    => 'نهائي',
                        'archived' => 'مؤرشف',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('ai_summarize')
                        ->label(__('ai.actions.summarize_document'))
                        ->icon('heroicon-o-cpu-chip')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (Document $record) {
                            AIProcessJob::dispatch($record, 'summarize_document', 'ar', auth()->id());
                            Notification::make()->title(__('ai.request_queued'))->success()->send();
                        }),

                    Tables\Actions\Action::make('ai_analyze')
                        ->label(__('ai.actions.analyze_contract'))
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Document $record) {
                            AIProcessJob::dispatch($record, 'analyze_contract', 'ar', auth()->id());
                            Notification::make()->title(__('ai.request_queued'))->success()->send();
                        }),
                ])->label(__('ai.ai_analysis'))->icon('heroicon-o-sparkles'),

                Tables\Actions\DeleteAction::make()->label('حذف'),
                Tables\Actions\RestoreAction::make()->label('استعادة'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('حذف نهائي'),
                    Tables\Actions\RestoreBulkAction::make()->label('استعادة المحدد'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['uploadedBy']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit'   => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
