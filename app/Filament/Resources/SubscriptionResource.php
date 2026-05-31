<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Plan;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?int    $navigationSort  = 5;

    public static function getModelLabel(): string       { return 'اشتراك'; }
    public static function getPluralModelLabel(): string  { return 'الاشتراكات'; }
    public static function getNavigationLabel(): string   { return 'اشتراكات المكاتب'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('تفاصيل الاشتراك')->schema([
                Forms\Components\Select::make('plan_id')
                    ->label('الخطة')
                    ->options(Plan::all()->mapWithKeys(fn ($p) => [$p->id => $p->getTranslation('name', 'ar')]))
                    ->required(),
                Forms\Components\Select::make('status')->label('الحالة')->options([
                    'trial' => 'تجربة مجانية', 'active' => 'نشط', 'past_due' => 'متأخر السداد',
                    'cancelled' => 'ملغى', 'expired' => 'منتهٍ',
                ])->required(),
                Forms\Components\Select::make('billing_cycle')->label('الدورة')->options(['monthly' => 'شهري', 'yearly' => 'سنوي'])->required(),
                Forms\Components\DateTimePicker::make('trial_ends_at')->label('انتهاء التجربة'),
                Forms\Components\DateTimePicker::make('current_period_start')->label('بداية الفترة'),
                Forms\Components\DateTimePicker::make('current_period_end')->label('نهاية الفترة'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('office.name')
                    ->label('المكتب')
                    ->getStateUsing(fn ($record) => $record->office?->getTranslation('name', 'ar') ?? '—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('الخطة')
                    ->getStateUsing(fn ($record) => $record->plan?->getTranslation('name', 'ar') ?? '—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->status_label)
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'trial'    => 'info',
                        'past_due' => 'warning',
                        default    => 'danger',
                    }),
                Tables\Columns\TextColumn::make('billing_cycle')->label('الدورة')
                    ->formatStateUsing(fn ($state) => $state === 'yearly' ? 'سنوي' : 'شهري'),
                Tables\Columns\TextColumn::make('days_left')
                    ->label('أيام متبقية')
                    ->getStateUsing(fn ($record) => $record->daysLeft() . ' يوم'),
                Tables\Columns\TextColumn::make('current_period_end')->label('ينتهي في')->date('Y/m/d')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('الحالة')->options([
                    'trial' => 'تجربة مجانية', 'active' => 'نشط', 'past_due' => 'متأخر السداد',
                    'cancelled' => 'ملغى', 'expired' => 'منتهٍ',
                ]),
                Tables\Filters\SelectFilter::make('plan_id')->label('الخطة')
                    ->options(Plan::all()->mapWithKeys(fn ($p) => [$p->id => $p->getTranslation('name', 'ar')])),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('تفعيل')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Subscription $record) => $record->status !== 'active')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Select::make('billing_cycle')->label('الدورة')->options(['monthly' => 'شهري', 'yearly' => 'سنوي'])->default('monthly')->required(),
                    ])
                    ->action(function (Subscription $record, array $data) {
                        $end = $data['billing_cycle'] === 'yearly' ? now()->addYear() : now()->addMonth();
                        $record->update([
                            'status'               => 'active',
                            'billing_cycle'        => $data['billing_cycle'],
                            'current_period_start' => now(),
                            'current_period_end'   => $end,
                        ]);
                    }),

                Tables\Actions\Action::make('extend')
                    ->label('تمديد شهر')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (Subscription $record) {
                        $base = $record->current_period_end && $record->current_period_end->isFuture()
                            ? $record->current_period_end : now();
                        $record->update(['status' => 'active', 'current_period_end' => $base->copy()->addMonth()]);
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('إلغاء')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Subscription $record) => $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->action(fn (Subscription $record) => $record->update(['status' => 'cancelled', 'cancelled_at' => now()])),

                Tables\Actions\EditAction::make()->label('تعديل'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SubscriptionResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['office', 'plan']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'edit'  => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
