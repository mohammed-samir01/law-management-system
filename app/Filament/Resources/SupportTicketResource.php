<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Filament\Resources\SupportTicketResource\RelationManagers;
use App\Models\SupportTicket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon  = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'تذاكر الدعم';
    protected static ?string $modelLabel      = 'تذكرة';
    protected static ?string $pluralModelLabel= 'تذاكر الدعم';
    protected static ?int    $navigationSort  = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) SupportTicket::where('status', 'open')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الزائر (من موقع الويب)')
                    ->schema([
                        Forms\Components\TextInput::make('visitor_name')
                            ->label('الاسم')
                            ->disabled(),
                        Forms\Components\TextInput::make('visitor_email')
                            ->label('البريد الإلكتروني')
                            ->disabled(),
                        Forms\Components\TextInput::make('visitor_phone')
                            ->label('الهاتف')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label('الرسالة')
                            ->disabled()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record && !empty($record->visitor_name))
                    ->collapsed(false),

                Forms\Components\Section::make(__('tickets.ticket'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('tickets.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category')
                            ->label(__('tickets.category'))
                            ->options([
                                'technical' => __('tickets.categories.technical'),
                                'billing'   => __('tickets.categories.billing'),
                                'legal'     => __('tickets.categories.legal'),
                                'general'   => __('tickets.categories.general'),
                            ])
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->label(__('tickets.priority'))
                            ->options([
                                'low'    => __('tickets.priorities.low'),
                                'medium' => __('tickets.priorities.medium'),
                                'high'   => __('tickets.priorities.high'),
                                'urgent' => __('tickets.priorities.urgent'),
                            ])
                            ->default('medium')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label(__('tickets.status'))
                            ->options([
                                'open'        => __('tickets.statuses.open'),
                                'in_progress' => __('tickets.statuses.in_progress'),
                                'resolved'    => __('tickets.statuses.resolved'),
                                'closed'      => __('tickets.statuses.closed'),
                            ])
                            ->default('open')
                            ->required(),

                        Forms\Components\Select::make('assigned_to')
                            ->label(__('tickets.assigned_to'))
                            ->options(
                                User::whereHas('roles', fn ($q) => $q->whereIn('name', ['office_admin', 'super_admin', 'lawyer', 'assistant']))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tickets.title'))
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category')
                    ->label(__('tickets.category'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'technical' => __('tickets.categories.technical'),
                        'billing'   => __('tickets.categories.billing'),
                        'legal'     => __('tickets.categories.legal'),
                        default     => __('tickets.categories.general'),
                    })
                    ->color('gray'),

                Tables\Columns\TextColumn::make('priority')
                    ->label(__('tickets.priority'))
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'urgent' => __('tickets.priorities.urgent'),
                        'high'   => __('tickets.priorities.high'),
                        'medium' => __('tickets.priorities.medium'),
                        'low'    => __('tickets.priorities.low'),
                        default  => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('tickets.status'))
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'open'        => 'danger',
                        'in_progress' => 'warning',
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($record) => $record->status_label),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label(__('tickets.assigned_to'))
                    ->default('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('visitor_name')
                    ->label('زائر الموقع')
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('tickets.created_by'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('replies_count')
                    ->label(__('tickets.replies'))
                    ->counts('replies')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('tickets.created_at'))
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('tickets.status'))
                    ->options([
                        'open'        => __('tickets.statuses.open'),
                        'in_progress' => __('tickets.statuses.in_progress'),
                        'resolved'    => __('tickets.statuses.resolved'),
                        'closed'      => __('tickets.statuses.closed'),
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('tickets.priority'))
                    ->options([
                        'urgent' => __('tickets.priorities.urgent'),
                        'high'   => __('tickets.priorities.high'),
                        'medium' => __('tickets.priorities.medium'),
                        'low'    => __('tickets.priorities.low'),
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label(__('tickets.category'))
                    ->options([
                        'technical' => __('tickets.categories.technical'),
                        'billing'   => __('tickets.categories.billing'),
                        'legal'     => __('tickets.categories.legal'),
                        'general'   => __('tickets.categories.general'),
                    ]),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),

                Tables\Actions\Action::make('assign_me')
                    ->label('إسناد لي')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn ($record) => ! $record->assigned_to)
                    ->action(function ($record) {
                        $record->update([
                            'assigned_to' => auth()->id(),
                            'status'      => 'in_progress',
                        ]);
                    }),

                Tables\Actions\Action::make('close')
                    ->label(__('tickets.close_ticket'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => ! in_array($record->status, ['closed', 'resolved']))
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'closed'])),

                Tables\Actions\Action::make('reopen')
                    ->label('إعادة فتح')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => in_array($record->status, ['closed', 'resolved']))
                    ->action(fn ($record) => $record->update(['status' => 'open'])),

                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
                Tables\Actions\RestoreAction::make()->label('استعادة'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\RestoreBulkAction::make()->label('استعادة المحدد'),

                    Tables\Actions\BulkAction::make('bulk_close')
                        ->label('إغلاق المحدد')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'closed'])),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['assignedTo', 'createdBy'])
            ->withCount('replies');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'view'   => Pages\ViewSupportTicket::route('/{record}'),
            'edit'   => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
