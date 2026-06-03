<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Addon;
use App\Models\Office;
use App\Models\OfficeAddon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string { return 'مكتب'; }
    public static function getPluralModelLabel(): string { return 'المكاتب'; }
    public static function getNavigationLabel(): string { return 'المكاتب'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات المكتب')->schema([
                Forms\Components\TextInput::make('name.ar')
                    ->label('الاسم (عربي)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name.en')
                    ->label('الاسم (إنجليزي)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('المعرف الفريد (Slug)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->alphaDash()
                    ->helperText('يُستخدم في الرابط — أحرف إنجليزية وأرقام وشرطات فقط'),
            ])->columns(2),

            Forms\Components\Section::make('بيانات التواصل')->schema([
                Forms\Components\TextInput::make('phone')
                    ->label('الهاتف')
                    ->tel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_number')
                    ->label('الرقم الضريبي')
                    ->maxLength(100),
            ])->columns(2),

            Forms\Components\Section::make('عنوان المكتب')->schema([
                Forms\Components\TextInput::make('address.street')
                    ->label('العنوان'),
                Forms\Components\TextInput::make('address.city')
                    ->label('المدينة'),
                Forms\Components\TextInput::make('address.governorate')
                    ->label('المحافظة'),
                Forms\Components\TextInput::make('address.country')
                    ->label('الدولة'),
            ])->columns(4)->collapsed(),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]),

            Forms\Components\Section::make('الإضافات المفعّلة')
                ->description('الإضافات المدفوعة المُفعّلة لهذا المكتب — استخدم زر "الإضافات" في قائمة المكاتب لتفعيل المزيد')
                ->icon('heroicon-o-puzzle-piece')
                ->visibleOn('edit')
                ->schema([
                    Forms\Components\Placeholder::make('active_addons_list')
                        ->label('')
                        ->content(function ($record) {
                            if (! $record) {
                                return '—';
                            }

                            $rows = OfficeAddon::with('addon')
                                ->where('office_id', $record->id)
                                ->orderByDesc('activated_at')
                                ->get();

                            if ($rows->isEmpty()) {
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="text-sm text-gray-400 py-2">لا توجد إضافات مفعّلة لهذا المكتب بعد.</div>'
                                );
                            }

                            $statusMap = [
                                'active'    => ['نشطة', 'background:#dcfce7;color:#15803d'],
                                'expired'   => ['منتهية', 'background:#fee2e2;color:#b91c1c'],
                                'cancelled' => ['ملغاة', 'background:#f3f4f6;color:#6b7280'],
                            ];

                            $html = '<div style="display:flex;flex-direction:column;gap:8px">';
                            foreach ($rows as $row) {
                                $name   = $row->addon?->getTranslation('name', 'ar') ?? '—';
                                $cycle  = $row->billing_cycle === 'yearly' ? 'سنوي' : 'شهري';
                                $expiry = $row->expires_at?->format('Y/m/d') ?? '—';
                                [$label, $style] = $statusMap[$row->status] ?? [$row->status, ''];

                                $html .= '<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;border:1px solid #e5e7eb;border-radius:12px;background:#fff">'
                                    . '<div style="display:flex;align-items:center;gap:10px">'
                                    . '<span style="font-weight:600;font-size:13px;color:#111827">' . e($name) . '</span>'
                                    . '<span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:9999px;' . $style . '">' . $label . '</span>'
                                    . '</div>'
                                    . '<div style="display:flex;align-items:center;gap:16px;font-size:12px;color:#6b7280">'
                                    . '<span>' . $cycle . '</span>'
                                    . '<span>ينتهي: ' . e($expiry) . '</span>'
                                    . '</div>'
                                    . '</div>';
                            }
                            $html .= '</div>';

                            return new \Illuminate\Support\HtmlString($html);
                        })
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('الدومين المخصص')
                ->description('يُضبط من لوحة المكتب — للقراءة فقط هنا')
                ->schema([
                    Forms\Components\Placeholder::make('custom_domain')
                        ->label('الدومين')
                        ->content(fn ($record) => $record?->custom_domain
                            ? new \Illuminate\Support\HtmlString('<span class="font-mono" dir="ltr">' . e($record->custom_domain) . '</span>')
                            : '—'),
                    Forms\Components\Placeholder::make('domain_verified_at')
                        ->label('تاريخ التحقق')
                        ->content(fn ($record) => $record?->domain_verified_at
                            ? new \Illuminate\Support\HtmlString('<span class="text-green-600 font-semibold">✓ ' . e($record->domain_verified_at->format('Y-m-d H:i')) . '</span>')
                            : new \Illuminate\Support\HtmlString('<span class="text-amber-600">غير مُفعَّل</span>')),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar') ?: $record->getTranslation('name', 'en'))
                    ->searchable(query: fn ($query, $search) => $query->where('name->ar', 'like', "%{$search}%")->orWhere('name->en', 'like', "%{$search}%"))
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => '<span dir="ltr">' . e($state) . '</span>')
                    ->html(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->searchable(),
                Tables\Columns\TextColumn::make('custom_domain')
                    ->label('الدومين المخصص')
                    ->placeholder('—')
                    ->searchable()
                    ->extraAttributes(['dir' => 'ltr', 'class' => 'font-mono text-sm'])
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('domain_verified_at')
                    ->label('دومين مُفعَّل')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->domain_verified_at !== null)
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الحالة'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('manage_addons')
                    ->label('الإضافات')
                    ->icon('heroicon-o-puzzle-piece')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('addon_id')
                            ->label('الإضافة')
                            ->options(fn () => Addon::where('is_active', true)->get()
                                ->mapWithKeys(fn ($a) => [$a->id => $a->getTranslation('name', 'ar') . ' — ' . number_format($a->price_monthly) . ' ج.م/شهر'])
                            )
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('billing_cycle')
                            ->label('الدورة')
                            ->options(['monthly' => 'شهري', 'yearly' => 'سنوي'])
                            ->default('monthly')
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $set('expires_at', $state === 'yearly'
                                    ? now()->addYear()->format('Y-m-d')
                                    : now()->addMonth()->format('Y-m-d')
                                )
                            )
                            ->required(),
                        Forms\Components\DatePicker::make('expires_at')
                            ->label('ينتهي في')
                            ->default(now()->addMonth()->format('Y-m-d'))
                            ->required(),
                    ])
                    ->action(function (Office $record, array $data) {
                        OfficeAddon::updateOrCreate(
                            ['office_id' => $record->id, 'addon_id' => $data['addon_id']],
                            [
                                'status'        => 'active',
                                'billing_cycle' => $data['billing_cycle'],
                                'activated_at'  => now(),
                                'expires_at'    => $data['expires_at'],
                                'cancelled_at'  => null,
                            ]
                        );
                        Notification::make()->title('تم تفعيل الإضافة للمكتب ✓')->success()->send();
                    })
                    ->modalHeading(fn (Office $record) => 'تفعيل إضافة — ' . $record->getTranslation('name', 'ar'))
                    ->modalSubmitActionLabel('تفعيل'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit'   => Pages\EditOffice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}
