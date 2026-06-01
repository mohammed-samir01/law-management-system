<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Jobs\AIProcessJob;
use App\Models\Document;
use App\Models\DocumentTemplate;
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
    use \App\Filament\Concerns\OfficeOnlyResource;

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
                                    return LegalCase::get()->mapWithKeys(fn ($c) => [
                                        $c->id => $c->case_number . ' — ' . ($c->getTranslation('title', 'ar') ?: $c->getTranslation('title', 'en'))
                                    ]);
                                }
                                if ($type === Hearing::class) {
                                    return Hearing::with('legalCase')
                                        ->get()
                                        ->mapWithKeys(fn ($h) => [
                                            $h->id => ($h->legalCase?->case_number ?? '-') . ' — ' . $h->scheduled_at?->format('Y/m/d'),
                                        ]);
                                }
                                return [];
                            }),
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
            ->defaultSort('created_at', 'desc')
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
            ->headerActions([
                Tables\Actions\Action::make('from_template')
                    ->label('إنشاء من قالب')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('template_id')
                            ->label('اختر القالب')
                            ->options(
                                DocumentTemplate::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(fn ($t) => [$t->id => $t->getTranslation('name', 'ar') ?: $t->getTranslation('name', 'en')])
                            )
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('placeholders', [])),

                        Forms\Components\Grid::make(2)
                            ->schema(function (Forms\Get $get) {
                                $templateId = $get('template_id');
                                if (! $templateId) return [];

                                $template = DocumentTemplate::find($templateId);
                                if (! $template || empty($template->placeholders)) return [];

                                return collect($template->placeholders)
                                    ->map(fn ($ph) => Forms\Components\TextInput::make('placeholders.' . ($ph['key'] ?? ''))
                                        ->label($ph['label'] ?? $ph['key'])
                                        ->default($ph['default'] ?? '')
                                        ->required(empty($ph['default']))
                                    )
                                    ->toArray();
                            })
                            ->visible(fn (Forms\Get $get) => (bool) $get('template_id')),

                        Forms\Components\Select::make('case_id')
                            ->label('ربط بقضية (اختياري)')
                            ->options(
                                LegalCase::get()->mapWithKeys(fn ($c) => [
                                    $c->id => $c->case_number . ' — ' . ($c->getTranslation('title', 'ar') ?: $c->getTranslation('title', 'en'))
                                ])
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (Forms\Get $get) => (bool) $get('template_id')),
                    ])
                    ->action(function (array $data) {
                        $template = DocumentTemplate::findOrFail($data['template_id']);
                        $placeholders = $data['placeholders'] ?? [];

                        $content = $template->content;
                        foreach ($placeholders as $key => $value) {
                            $content = str_replace('{{' . $key . '}}', $value, $content);
                        }

                        $titleAr = $template->getTranslation('name', 'ar') . ' — ' . now()->format('Y/m/d');

                        $doc = Document::create([
                            'office_id'         => auth()->user()->office_id,
                            'title'             => ['ar' => $titleAr, 'en' => $template->getTranslation('name', 'en')],
                            'type'              => $this->mapCategoryToType($template->category),
                            'category'          => $template->category,
                            'status'            => 'draft',
                            'version'           => 1,
                            'uploaded_by'       => auth()->id(),
                            'documentable_type' => ! empty($data['case_id']) ? LegalCase::class : null,
                            'documentable_id'   => $data['case_id'] ?? null,
                            'content'           => ['ar' => $content, 'en' => ''],
                        ]);

                        Notification::make()
                            ->title('تم إنشاء الوثيقة من القالب')
                            ->body($titleAr)
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),

                Tables\Actions\Action::make('export_pdf')
                    ->label('تصدير PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Document $record) => route('documents.pdf', $record))
                    ->openUrlInNewTab(),

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

    private static function mapCategoryToType(string $category): string
    {
        return match($category) {
            'legal'     => 'contract',
            'financial' => 'other',
            'court'     => 'pleading',
            'contract'  => 'contract',
            default     => 'other',
        };
    }
}
