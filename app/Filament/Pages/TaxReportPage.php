<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class TaxReportPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'المالية';
    protected static ?string $navigationLabel = 'التقرير الضريبي';
    protected static ?string $title           = 'التقرير الضريبي';
    protected static ?int    $navigationSort   = 8;
    protected static string  $view            = 'filament.pages.tax-report';

    public ?array $data = [];
    public ?array $result = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->office?->hasAddon('einvoice-tax') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function mount(): void
    {
        $this->form->fill([
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to'   => now()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\DatePicker::make('from')->label(__('addons.reports_date_from'))->required(),
                Forms\Components\DatePicker::make('to')->label(__('addons.reports_date_to'))->required(),
            ]),
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('generate')
                    ->label(__('addons.reports_generate'))
                    ->icon('heroicon-o-play')
                    ->action('generate'),
            ]),
        ])->statePath('data');
    }

    public function generate(): void
    {
        $s    = $this->form->getState();
        $from = Carbon::parse($s['from'])->startOfDay();
        $to   = Carbon::parse($s['to'])->endOfDay();

        $q = Invoice::query() // office scope applies
            ->whereBetween('created_at', [$from, $to]);

        $this->result = [
            'count'      => (clone $q)->count(),
            'total'      => number_format((float) (clone $q)->sum('total_amount'), 2),
            'vat'        => number_format((float) (clone $q)->sum('tax_amount'), 2),
            'net'        => number_format((float) (clone $q)->sum('amount'), 2),
            'vat_paid'   => number_format((float) (clone $q)->where('status', 'paid')->sum('tax_amount'), 2),
        ];
    }
}
