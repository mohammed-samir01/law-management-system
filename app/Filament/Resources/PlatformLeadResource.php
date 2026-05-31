<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformLeadResource\Pages;
use App\Models\PlatformLead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlatformLeadResource extends Resource
{
    protected static ?string $model = PlatformLead::class;

    protected static ?string $navigationIcon  = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?int    $navigationSort   = 6;

    public static function getModelLabel(): string      { return 'رسالة'; }
    public static function getPluralModelLabel(): string { return 'رسائل التواصل'; }
    public static function getNavigationLabel(): string  { return 'رسائل التواصل'; }

    public static function getNavigationBadge(): ?string
    {
        return (string) (PlatformLead::where('status', 'new')->count() ?: '') ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('الرسالة')->schema([
                Forms\Components\TextInput::make('name')->label('الاسم')->disabled(),
                Forms\Components\TextInput::make('email')->label('البريد')->disabled(),
                Forms\Components\TextInput::make('phone')->label('الهاتف')->disabled(),
                Forms\Components\TextInput::make('subject')->label('الموضوع')->disabled(),
                Forms\Components\Textarea::make('message')->label('نص الرسالة')->disabled()->rows(5)->columnSpanFull(),
                Forms\Components\Select::make('status')->label('الحالة')->options([
                    'new' => 'جديدة', 'read' => 'مقروءة', 'replied' => 'تم الرد', 'closed' => 'مغلقة',
                ])->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('البريد')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->label('الهاتف')->placeholder('—'),
                Tables\Columns\TextColumn::make('subject')->label('الموضوع')->limit(30)->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                    ->formatStateUsing(fn ($record) => $record->status_label)
                    ->color(fn ($state) => match ($state) {
                        'new' => 'danger', 'read' => 'warning', 'replied' => 'success', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('الحالة')->options([
                    'new' => 'جديدة', 'read' => 'مقروءة', 'replied' => 'تم الرد', 'closed' => 'مغلقة',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض')
                    ->after(fn (PlatformLead $record) => $record->status === 'new' ? $record->update(['status' => 'read']) : null),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformLeads::route('/'),
            'view'  => Pages\ViewPlatformLead::route('/{record}'),
            'edit'  => Pages\EditPlatformLead::route('/{record}/edit'),
        ];
    }
}
