<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Support\Pdf;

class AdvancedReportsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'التقارير والتحليلات';
    protected static ?string $title           = 'التقارير والتحليلات المتقدمة';
    protected static ?int    $navigationSort   = 9;
    protected static string  $view            = 'filament.pages.advanced-reports';

    public ?array $data = [];

    /** @var array{title:string, rows:array}|null */
    public ?array $report = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->office?->hasAddon('advanced-reports') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->office?->hasAddon('advanced-reports') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'type' => 'financial',
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to'   => now()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('type')
                        ->label(__('addons.reports_type'))
                        ->options([
                            'financial' => __('addons.reports_financial'),
                            'cases'     => __('addons.reports_cases'),
                            'lawyers'   => __('addons.reports_lawyers'),
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('from')->label(__('addons.reports_date_from'))->required(),
                    Forms\Components\DatePicker::make('to')->label(__('addons.reports_date_to'))->required(),
                ]),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('generate')
                        ->label(__('addons.reports_generate'))
                        ->icon('heroicon-o-play')
                        ->action('generate'),
                    Forms\Components\Actions\Action::make('exportPdf')
                        ->label(__('addons.reports_export_pdf'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action('exportPdf'),
                ]),
            ])
            ->statePath('data');
    }

    public function generate(): void
    {
        $s = $this->form->getState();

        $this->report = app(ReportService::class)->generate(
            $s['type'],
            auth()->user()->office_id,
            Carbon::parse($s['from'])->startOfDay(),
            Carbon::parse($s['to'])->endOfDay(),
        );
    }

    public function exportPdf()
    {
        $this->generate();

        $bytes = Pdf::make('pdf.report', [
            'report'     => $this->report,
            'officeName' => auth()->user()->office?->name ?? config('app.name'),
        ]);

        return response()->streamDownload(
            fn () => print($bytes),
            'report-' . now()->format('Ymd-His') . '.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

}
