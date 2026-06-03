<?php

namespace App\Filament\Resources\LegalCaseResource\RelationManagers;

use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimeEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'timeEntries';
    protected static ?string $title       = 'سجلّات الوقت';
    protected static ?string $label        = 'سجل وقت';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('minutes')->label(__('addons.time_minutes'))->numeric()->minValue(1)->required(),
            Forms\Components\TextInput::make('rate')->label(__('addons.time_rate'))->numeric()->minValue(0)->nullable(),
            Forms\Components\DatePicker::make('occurred_at')->label(__('addons.time_occurred_at'))->default(now())->required(),
            Forms\Components\Textarea::make('description')->label(__('addons.time_description'))->columnSpanFull(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')->label(__('addons.time_occurred_at'))->date('Y/m/d')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('بواسطة')->default('—'),
                Tables\Columns\TextColumn::make('minutes')->label(__('addons.time_minutes')),
                Tables\Columns\TextColumn::make('rate')->label(__('addons.time_rate'))->default('—'),
                Tables\Columns\TextColumn::make('amount')->label(__('addons.time_amount'))
                    ->state(fn ($record) => number_format($record->amount, 2)),
                Tables\Columns\IconColumn::make('billed')->label(__('addons.time_billed'))->boolean(),
                Tables\Columns\TextColumn::make('description')->label(__('addons.time_description'))->limit(40)->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('addons.time_add')),
                Tables\Actions\Action::make('invoice_unbilled')
                    ->label(__('addons.time_invoice_unbilled'))
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn () => $this->invoiceUnbilled()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل')->visible(fn ($record) => ! $record->billed),
                Tables\Actions\DeleteAction::make()->label('حذف')->visible(fn ($record) => ! $record->billed),
            ]);
    }

    private function invoiceUnbilled(): void
    {
        $case = $this->getOwnerRecord();

        $entries = $case->timeEntries()->where('billed', false)->get();

        if ($entries->isEmpty()) {
            Notification::make()->title(__('addons.time_no_unbilled'))->warning()->send();
            return;
        }

        if (! $case->client_id) {
            Notification::make()->title('لا يوجد عميل مرتبط بالقضية')->danger()->send();
            return;
        }

        $total = round($entries->sum(fn ($e) => $e->amount), 2);

        \Illuminate\Support\Facades\DB::transaction(function () use ($case, $entries, $total) {
            $invoice = Invoice::create([
                'office_id'      => $case->office_id,
                'client_id'      => $case->client_id,
                'case_id'        => $case->id,
                'invoice_number' => 'INV-' . str_pad(Invoice::withoutGlobalScopes()->count() + 1, 5, '0', STR_PAD_LEFT),
                'amount'         => $total,
                'tax_amount'     => 0,
                'total_amount'   => $total,
                'currency'       => 'EGP',
                'status'         => 'sent',
                'due_date'       => now()->addDays(14),
                'created_by'     => auth()->id(),
            ]);

            $entries->each->update(['billed' => true, 'invoice_id' => $invoice->id]);
        });

        Notification::make()->title(__('addons.time_invoice_created'))->success()->send();
    }
}
