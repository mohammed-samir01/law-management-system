<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalCaseResource\Pages;
use App\Jobs\AIProcessJob;
use App\Models\Client;
use App\Models\LegalCase;
use App\Models\User;
use App\Services\PDFService;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LegalCaseResource extends Resource
{
    use \App\Filament\Concerns\OfficeOnlyResource;

    protected static ?string $model = LegalCase::class;
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'القضايا';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string { return 'قضية'; }
    public static function getPluralModelLabel(): string { return 'القضايا'; }
    public static function getNavigationLabel(): string { return 'القضايا'; }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->getTranslation('title', 'ar') ?: $record->case_number;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['case_number', 'court', 'judge'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات القضية')->schema([
                Forms\Components\TextInput::make('case_number')
                    ->label('رقم القضية')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'CASE-' . strtoupper(Str::random(8)))
                    ->maxLength(50),
                Forms\Components\Select::make('type')
                    ->label('نوع القضية')
                    ->options([
                        'civil'          => 'مدنية',
                        'criminal'       => 'جنائية',
                        'family'         => 'أسرة',
                        'labor'          => 'عمالية',
                        'commercial'     => 'تجارية',
                        'administrative' => 'إدارية',
                        'real_estate'    => 'عقارية',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('title.ar')
                    ->label('عنوان القضية (عربي)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('title.en')
                    ->label('عنوان القضية (إنجليزي)')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'new'      => 'جديدة',
                        'active'   => 'نشطة',
                        'pending'  => 'معلقة',
                        'adjourned'=> 'مؤجلة',
                        'closed'   => 'مغلقة',
                        'archived' => 'مؤرشفة',
                    ])
                    ->default('new')
                    ->required()
                    ->live(),
                Forms\Components\Select::make('client_id')
                    ->label('العميل')
                    ->options(
                        Client::withoutGlobalScopes()->get()
                            ->mapWithKeys(fn ($c) => [$c->id => $c->getTranslation('name', 'ar') ?: $c->getTranslation('name', 'en')])
                    )
                    ->searchable()
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('بيانات المحكمة')->schema([
                Forms\Components\TextInput::make('court')
                    ->label('المحكمة')
                    ->maxLength(255),
                Forms\Components\TextInput::make('judge')
                    ->label('القاضي')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('closed_at')
                    ->label('تاريخ الإغلاق')
                    ->nullable()
                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['closed', 'archived'])),
            ])->columns(2),

            Forms\Components\Section::make('المحامون المعينون')->schema([
                Forms\Components\Select::make('lawyers')
                    ->label('المحامون')
                    ->multiple()
                    ->relationship(
                        'lawyers',
                        'name',
                        fn ($query) => $query->whereHas('roles', fn ($q) => $q->whereIn('name', ['lawyer', 'office_admin']))
                    )
                    ->searchable()
                    ->preload(),
            ]),

            Forms\Components\Section::make('وصف القضية')->schema([
                Forms\Components\Textarea::make('description.ar')
                    ->label('الوصف (عربي)')
                    ->rows(4),
                Forms\Components\Textarea::make('description.en')
                    ->label('الوصف (إنجليزي)')
                    ->rows(4),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('case_number')
                    ->label('رقم القضية')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', 'ar') ?: $record->getTranslation('title', 'en'))
                    ->searchable(query: fn ($query, $search) => $query->whereRaw("JSON_EXTRACT(title, '$.ar') LIKE ?", ["%{$search}%"]))
                    ->limit(40),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'civil' => 'مدنية', 'criminal' => 'جنائية', 'family' => 'أسرة',
                        'labor' => 'عمالية', 'commercial' => 'تجارية', 'administrative' => 'إدارية',
                        'real_estate' => 'عقارية',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'new' => 'جديدة', 'active' => 'نشطة', 'pending' => 'معلقة',
                        'adjourned' => 'مؤجلة', 'closed' => 'مغلقة', 'archived' => 'مؤرشفة',
                        default => $state,
                    })
                    ->colors([
                        'info'    => 'new',
                        'success' => 'active',
                        'warning' => 'pending',
                        'primary' => 'adjourned',
                        'danger'  => 'closed',
                        'gray'    => 'archived',
                    ]),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->getStateUsing(fn ($record) => $record->client?->getTranslation('name', 'ar'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('court')
                    ->label('المحكمة')
                    ->searchable()
                    ->toggleable(),
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
                        'new' => 'جديدة', 'active' => 'نشطة', 'pending' => 'معلقة',
                        'adjourned' => 'مؤجلة', 'closed' => 'مغلقة', 'archived' => 'مؤرشفة',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'civil' => 'مدنية', 'criminal' => 'جنائية', 'family' => 'أسرة',
                        'labor' => 'عمالية', 'commercial' => 'تجارية', 'administrative' => 'إدارية',
                        'real_estate' => 'عقارية',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('close')
                    ->label('إغلاق القضية')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => !in_array($record->status, ['closed', 'archived']))
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'closed', 'closed_at' => now()])),
                Tables\Actions\Action::make('pdf_report')
                    ->label('تقرير PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function ($record) {
                        $path = app(PDFService::class)->generateCaseReportPDF($record);
                        return response()->download(storage_path('app/public/'.$path));
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('ai_summarize_case')
                        ->label(__('ai.actions.summarize_case'))
                        ->icon('heroicon-o-cpu-chip')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (LegalCase $record) {
                            AIProcessJob::dispatch($record, 'summarize_case', 'ar', auth()->id());
                            Notification::make()->title(__('ai.request_queued'))->success()->send();
                        }),

                    Tables\Actions\Action::make('ai_strategy')
                        ->label(__('ai.actions.suggest_strategy'))
                        ->icon('heroicon-o-light-bulb')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (LegalCase $record) {
                            AIProcessJob::dispatch($record, 'suggest_strategy', 'ar', auth()->id());
                            Notification::make()->title(__('ai.request_queued'))->success()->send();
                        }),
                ])->label(__('ai.ai_analysis'))->icon('heroicon-o-sparkles'),
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
            'index'  => Pages\ListLegalCases::route('/'),
            'create' => Pages\CreateLegalCase::route('/create'),
            'view'   => Pages\ViewLegalCase::route('/{record}'),
            'edit'   => Pages\EditLegalCase::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['client', 'lawyers'])
            ->withoutGlobalScopes([\Illuminate\Database\Eloquent\SoftDeletingScope::class]);
    }
}
