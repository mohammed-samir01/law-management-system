<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AIResultResource\Pages;
use App\Jobs\AIProcessJob;
use App\Models\AIResult;
use App\Models\Document;
use App\Models\LegalCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AIResultResource extends Resource
{
    use \App\Filament\Concerns\OfficeOnlyResource;

    protected static ?string $model = AIResult::class;

    protected static ?string $navigationIcon  = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'نتائج الذكاء الاصطناعي';
    protected static ?string $modelLabel      = 'نتيجة ذكاء اصطناعي';
    protected static ?string $pluralModelLabel= 'نتائج الذكاء الاصطناعي';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('ai.new_request'))
                ->schema([
                    Forms\Components\Select::make('action')
                        ->label(__('ai.action'))
                        ->options([
                            'summarize_document' => __('ai.actions.summarize_document'),
                            'analyze_contract'   => __('ai.actions.analyze_contract'),
                            'summarize_case'     => __('ai.actions.summarize_case'),
                            'suggest_strategy'   => __('ai.actions.suggest_strategy'),
                        ])
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('language')
                        ->label(__('ai.language'))
                        ->options(['ar' => 'العربية', 'en' => 'English'])
                        ->default('ar')
                        ->required(),

                    Forms\Components\Select::make('document_id')
                        ->label(__('ai.document'))
                        ->options(Document::pluck('id', 'id')->mapWithKeys(fn ($id) => [$id => Document::find($id)?->getTranslation('title', 'ar') ?: "وثيقة #{$id}"]))
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Forms\Get $get) => in_array($get('action'), ['summarize_document', 'analyze_contract'])),

                    Forms\Components\Select::make('case_id')
                        ->label(__('ai.case'))
                        ->options(LegalCase::pluck('case_number', 'id'))
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Forms\Get $get) => in_array($get('action'), ['summarize_case', 'suggest_strategy'])),
                ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make(__('ai.result_details'))
                ->schema([
                    Infolists\Components\TextEntry::make('result_type')
                        ->label(__('ai.action'))
                        ->formatStateUsing(fn ($state) => match($state) {
                            'document_summary' => __('ai.actions.summarize_document'),
                            'contract_analysis'=> __('ai.actions.analyze_contract'),
                            'case_summary'     => __('ai.actions.summarize_case'),
                            'strategy_suggestion'=> __('ai.actions.suggest_strategy'),
                            default            => $state,
                        })
                        ->badge()
                        ->color('primary'),

                    Infolists\Components\TextEntry::make('model_used')
                        ->label(__('ai.model_used')),

                    Infolists\Components\TextEntry::make('tokens_used')
                        ->label(__('ai.tokens_used')),

                    Infolists\Components\TextEntry::make('created_at')
                        ->label(__('ai.created_at'))
                        ->dateTime('Y/m/d H:i'),
                ])->columns(4),

            Infolists\Components\Section::make(__('ai.result_content'))
                ->schema([
                    Infolists\Components\TextEntry::make('content')
                        ->label('')
                        ->prose()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('result_type')
                    ->label(__('ai.action'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        'document_summary'   => __('ai.actions.summarize_document'),
                        'contract_analysis'  => __('ai.actions.analyze_contract'),
                        'case_summary'       => __('ai.actions.summarize_case'),
                        'strategy_suggestion'=> __('ai.actions.suggest_strategy'),
                        default              => $state,
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('content')
                    ->label(__('ai.result_content'))
                    ->limit(80),

                Tables\Columns\TextColumn::make('model_used')
                    ->label(__('ai.model_used'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tokens_used')
                    ->label(__('ai.tokens_used'))
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('بواسطة')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('result_type')
                    ->label(__('ai.action'))
                    ->options([
                        'document_summary'   => __('ai.actions.summarize_document'),
                        'contract_analysis'  => __('ai.actions.analyze_contract'),
                        'case_summary'       => __('ai.actions.summarize_case'),
                        'strategy_suggestion'=> __('ai.actions.suggest_strategy'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['createdBy']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAIResults::route('/'),
            'create' => Pages\CreateAIResult::route('/create'),
            'view'   => Pages\ViewAIResult::route('/{record}'),
        ];
    }
}
