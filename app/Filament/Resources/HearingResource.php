<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HearingResource\Pages;
use App\Models\Hearing;
use App\Models\LegalCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HearingResource extends Resource
{
    use \App\Filament\Concerns\OfficeOnlyResource;

    protected static ?string $model = Hearing::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'القضايا';
    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string { return 'جلسة'; }
    public static function getPluralModelLabel(): string { return 'الجلسات'; }
    public static function getNavigationLabel(): string { return 'الجلسات'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الجلسة')->schema([
                Forms\Components\Select::make('case_id')
                    ->label('القضية')
                    ->options(
                        LegalCase::withoutGlobalScopes()->get()
                            ->mapWithKeys(fn ($c) => [$c->id => $c->case_number . ' — ' . ($c->getTranslation('title', 'ar') ?: $c->getTranslation('title', 'en'))])
                    )
                    ->searchable()
                    ->required(),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('موعد الجلسة')
                    ->required()
                    ->seconds(false),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'scheduled'  => 'مجدولة',
                        'held'       => 'منعقدة',
                        'completed'  => 'منتهية',
                        'adjourned'  => 'مرفوعة',
                        'postponed'  => 'مؤجلة',
                        'cancelled'  => 'ملغاة',
                    ])
                    ->default('scheduled')
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('تفاصيل المحكمة')->schema([
                Forms\Components\TextInput::make('location')
                    ->label('الموقع / المحكمة')
                    ->maxLength(255),
                Forms\Components\TextInput::make('court_room')
                    ->label('قاعة المحكمة')
                    ->maxLength(100),
                Forms\Components\TextInput::make('judge')
                    ->label('القاضي')
                    ->maxLength(255),
            ])->columns(3),

            Forms\Components\Section::make('ملاحظات ونتيجة الجلسة')->schema([
                Forms\Components\Textarea::make('notes.ar')
                    ->label('ملاحظات (عربي)')
                    ->rows(3),
                Forms\Components\Textarea::make('outcome.ar')
                    ->label('نتيجة الجلسة (عربي)')
                    ->rows(3),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('الموعد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('legalCase.case_number')
                    ->label('رقم القضية')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('legalCase.title')
                    ->label('القضية')
                    ->getStateUsing(fn ($record) => $record->legalCase?->getTranslation('title', 'ar'))
                    ->limit(35),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'scheduled'  => 'مجدولة',
                        'held'       => 'منعقدة',
                        'completed'  => 'منتهية',
                        'adjourned'  => 'مرفوعة',
                        'postponed'  => 'مؤجلة',
                        'cancelled'  => 'ملغاة',
                        default      => $state,
                    })
                    ->colors([
                        'info'    => 'scheduled',
                        'primary' => 'held',
                        'success' => ['completed', 'held'],
                        'warning' => ['postponed', 'adjourned'],
                        'danger'  => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('location')
                    ->label('الموقع')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('judge')
                    ->label('القاضي')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'scheduled' => 'مجدولة', 'completed' => 'منتهية',
                        'postponed' => 'مؤجلة', 'cancelled' => 'ملغاة',
                    ]),
                Tables\Filters\Filter::make('upcoming')
                    ->label('القادمة فقط')
                    ->query(fn ($query) => $query->where('scheduled_at', '>=', now())),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete')
                    ->label('إنهاء الجلسة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'scheduled')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'completed'])),
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
            'index'  => Pages\ListHearings::route('/'),
            'create' => Pages\CreateHearing::route('/create'),
            'edit'   => Pages\EditHearing::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['legalCase'])
            ->withoutGlobalScopes([\Illuminate\Database\Eloquent\SoftDeletingScope::class]);
    }
}
