<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\ExpenseOps;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartCombinedFinancialWidget extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Combined Financial Overview';

    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    // Filter options
    public ?string $expenseFilter = 'total_amount';

    public ?string $revenueFilter = 'revenue';

    public ?string $timeFrame = 'last_12_months';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?string $chartType = 'dual'; // 'dual' or 'stacked'

    // Constants for filter keys to avoid magic strings
    private const REVENUE_FILTER_REVENUE = 'revenue';

    private const REVENUE_FILTER_PROMO = 'promo';

    private const REVENUE_FILTER_ADDITIONAL = 'additional';

    private const REVENUE_FILTER_REDUCTION = 'reduction';

    private const EXPENSE_FILTER_TOTAL_AMOUNT = 'total_amount';

    private const EXPENSE_FILTER_COUNT = 'count_expenses';

    private const EXPENSE_FILTER_AVG_AMOUNT = 'avg_amount';

    protected function getType(): string
    {
        return 'bar'; // 'line' can also be used, 'bar' is good for dual/stacked
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Select::make('chartType')
                            ->label('Chart Style')
                            ->options([
                                'dual' => 'Dual Axis',
                                'stacked' => 'Stacked Bar',
                            ])
                            ->default('dual')
                            ->live(),

                        Select::make('revenueFilter')
                            ->label('Revenue Metric')
                            ->options([
                                self::REVENUE_FILTER_REVENUE => 'Total Revenue',
                                self::REVENUE_FILTER_PROMO => 'Promo Deductions',
                                self::REVENUE_FILTER_ADDITIONAL => 'Additional Charges',
                                self::REVENUE_FILTER_REDUCTION => 'Reductions',
                            ])
                            ->default(self::REVENUE_FILTER_REVENUE)
                            ->live(),

                        Select::make('expenseFilter')
                            ->label('Expense Metric')
                            ->options([
                                self::EXPENSE_FILTER_TOTAL_AMOUNT => 'Total Expenses Ops',
                                self::EXPENSE_FILTER_COUNT => 'Number of Expenses Ops',
                                self::EXPENSE_FILTER_AVG_AMOUNT => 'Average Expense Ops',
                            ])
                            ->default(self::EXPENSE_FILTER_TOTAL_AMOUNT)
                            ->live(),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('timeFrame')
                            ->label('Time Frame')
                            ->options([
                                'last_12_months' => 'Last 12 Months',
                                'last_6_months' => 'Last 6 Months',
                                'this_year' => 'This Year',
                                'last_year' => 'Last Year',
                                'custom' => 'Custom Range',
                            ])
                            ->default('last_12_months')
                            ->live(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('startDate')
                                    ->label('Start Date')
                                    ->visible(fn (callable $get) => $get('timeFrame') === 'custom')
                                    ->live(),
                                DatePicker::make('endDate')
                                    ->label('End Date')
                                    ->visible(fn (callable $get) => $get('timeFrame') === 'custom')
                                    ->live(),
                            ]),
                    ]),
            ]);
    }

    protected function getData(): array
    {
        try {
            [$startDate, $endDate] = $this->getDateRange();

            // Get revenue data
            $revenueData = $this->getRevenueData($startDate, $endDate);

            // Get expense data
            $expenseOpsData = $this->getExpenseData(ExpenseOps::class, $startDate, $endDate);

            // Get expense wedding data
            $expenseWeddingData = $this->getExpenseData(Expense::class, $startDate, $endDate);

            // Prepare data arrays
            $labels = [];
            $revenueValues = [];
            $expenseOpsValues = [];
            $expenseWeddingValues = [];

            // Generate all months between start and end date
            $currentDate = Carbon::parse($startDate);
            $endDateTime = Carbon::parse($endDate);

            while ($currentDate <= $endDateTime) {
                $yearMonth = $currentDate->format('Y-m');
                $monthLabel = $currentDate->format('M Y');

                $currentRevenue = $revenueData->get($yearMonth);
                $currentExpenseOps = $expenseOpsData->get($yearMonth);
                $currentExpenseWedding = $expenseWeddingData->get($yearMonth);

                $labels[] = $monthLabel;

                // Get revenue value based on filter
                $revenueValues[] = match ($this->revenueFilter) {
                    self::REVENUE_FILTER_PROMO => $currentRevenue->promo ?? 0,
                    self::REVENUE_FILTER_ADDITIONAL => $currentRevenue->additional ?? 0,
                    self::REVENUE_FILTER_REDUCTION => $currentRevenue->reduction ?? 0,
                    default => $currentRevenue->revenue ?? 0, // REVENUE_FILTER_REVENUE
                };

                // Get expense ops value based on filter
                $expenseOpsValues[] = match ($this->expenseFilter) {
                    self::EXPENSE_FILTER_COUNT => $currentExpenseOps->count_expenses ?? 0,
                    self::EXPENSE_FILTER_AVG_AMOUNT => $currentExpenseOps->avg_amount ?? 0,
                    default => $currentExpenseOps->total_amount ?? 0, // EXPENSE_FILTER_TOTAL_AMOUNT
                };
                // Get expense wedding value based on filter
                $expenseWeddingValues[] = match ($this->expenseFilter) {
                    self::EXPENSE_FILTER_COUNT => $currentExpenseWedding->count_expenses ?? 0,
                    self::EXPENSE_FILTER_AVG_AMOUNT => $currentExpenseWedding->avg_amount ?? 0,
                    default => $currentExpenseWedding->total_amount ?? 0, // EXPENSE_FILTER_TOTAL_AMOUNT
                };

                $currentDate->addMonth();
            }

            // Define color schemes
            $revenueColors = [
                self::REVENUE_FILTER_REVENUE => ['rgb(75, 192, 192)', 'rgba(75, 192, 192, 0.5)'], // Teal
                self::REVENUE_FILTER_PROMO => ['rgb(255, 99, 132)', 'rgba(255, 99, 132, 0.5)'], // Pink
                self::REVENUE_FILTER_ADDITIONAL => ['rgb(54, 162, 235)', 'rgba(54, 162, 235, 0.5)'], // Blue
                self::REVENUE_FILTER_REDUCTION => ['rgb(255, 206, 86)', 'rgba(255, 206, 86, 0.5)'], // Yellow
            ];

            $expenseColors = [
                self::EXPENSE_FILTER_TOTAL_AMOUNT => ['rgb(153, 102, 255)', 'rgba(153, 102, 255, 0.5)'], // Purple
                self::EXPENSE_FILTER_COUNT => ['rgb(255, 159, 64)', 'rgba(255, 159, 64, 0.5)'], // Orange
                self::EXPENSE_FILTER_AVG_AMOUNT => ['rgb(201, 203, 207)', 'rgba(201, 203, 207, 0.5)'], // Grey
            ];
            $weddingExpenseFixedColor = ['rgb(255, 99, 71)', 'rgba(255, 99, 71, 0.5)']; // Tomato Red for Wedding Expenses

            $selectedRevenueColors = $revenueColors[$this->revenueFilter] ?? $revenueColors[self::REVENUE_FILTER_REVENUE];
            $selectedExpenseOpsColors = $expenseColors[$this->expenseFilter] ?? $expenseColors[self::EXPENSE_FILTER_TOTAL_AMOUNT];

            // Prepare datasets based on chart type
            if ($this->chartType === 'stacked') {
                return [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => $this->getRevenueLabel(),
                            'data' => $revenueValues,
                            'backgroundColor' => $selectedRevenueColors[1],
                            'borderColor' => $selectedRevenueColors[0],
                            'borderWidth' => 1,
                        ],
                        [
                            'label' => $this->getExpenseOpsLabel(),
                            'data' => $expenseOpsValues,
                            'backgroundColor' => $selectedExpenseOpsColors[1],
                            'borderColor' => $selectedExpenseOpsColors[0],
                            'borderWidth' => 1,
                        ],
                        [
                            'label' => $this->getWeddingExpenseLabel(),
                            'data' => $expenseWeddingValues,
                            'backgroundColor' => $weddingExpenseFixedColor[1],
                            'borderColor' => $weddingExpenseFixedColor[0],
                            'borderWidth' => 1,
                        ],
                    ],
                ];
            }

            // Dual axis chart configuration
            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $this->getRevenueLabel(),
                        'data' => $revenueValues,
                        'borderColor' => $selectedRevenueColors[0],
                        'backgroundColor' => $selectedRevenueColors[1],
                        'yAxisID' => 'y',
                        'fill' => true,
                    ],
                    [
                        'label' => $this->getExpenseOpsLabel(),
                        'data' => $expenseOpsValues,
                        'borderColor' => $selectedExpenseOpsColors[0],
                        'backgroundColor' => $selectedExpenseOpsColors[1],
                        'yAxisID' => 'y1',
                        'fill' => true,
                    ],
                    [
                        'label' => $this->getWeddingExpenseLabel(),
                        'data' => $expenseWeddingValues,
                        'borderColor' => $weddingExpenseFixedColor[0],
                        'backgroundColor' => $weddingExpenseFixedColor[1],
                        'yAxisID' => 'y1',
                        'fill' => true, // Can be false if you prefer just lines for secondary axis
                    ],
                ],
                'options' => [
                    'scales' => [
                        'y' => [
                            'type' => 'linear',
                            'display' => true,
                            'position' => 'left',
                        ],
                        'y1' => [
                            'type' => 'linear',
                            'display' => true,
                            'position' => 'right',
                            'grid' => [
                                'drawOnChartArea' => false,
                            ],
                        ],
                    ],
                ],
            ];
        } catch (Exception $e) {
            Log::error('Error in ChartCombinedFinancialWidget: '.$e->getMessage());

            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
    }

    protected function getDateRange(): array
    {
        $now = Carbon::now();
        switch ($this->timeFrame) {
            case 'last_6_months':
                $startDate = $now->copy()->subMonths(5)->startOfMonth(); // Includes current month
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'last_year':
                $startDate = $now->copy()->subYear()->startOfYear();
                $endDate = $now->copy()->subYear()->endOfYear();
                break;
            case 'custom':
                $startDate = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : $now->copy()->subMonths(11)->startOfMonth();
                $endDate = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : $now->copy()->endOfMonth();
                break;
            case 'last_12_months':
            default:
                $startDate = $now->copy()->subMonths(11)->startOfMonth(); // Includes current month
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        return [$startDate, $endDate];
    }

    protected function getRevenueData(Carbon $startDate, Carbon $endDate)
    {
        return Order::select(
            DB::raw('YEAR(closing_date) as year'),
            DB::raw('MONTH(closing_date) as month'),
            DB::raw('SUM(total_price + COALESCE(penambahan,0) - COALESCE(pengurangan,0) - COALESCE(promo,0)) as revenue'),
            DB::raw('SUM(COALESCE(promo,0)) as promo'),
            DB::raw('SUM(COALESCE(penambahan,0)) as additional'),
            DB::raw('SUM(COALESCE(pengurangan,0)) as reduction')
        )
            ->whereBetween('closing_date', [$startDate, $endDate])
            ->whereNotNull('closing_date')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT));
    }

    protected function getExpenseData(string $modelClass, Carbon $startDate, Carbon $endDate)
    {
        return $modelClass::select(
            DB::raw('YEAR(date_expense) as year'),
            DB::raw('MONTH(date_expense) as month'),
            DB::raw('SUM(COALESCE(amount,0)) as total_amount'),
            DB::raw('COUNT(*) as count_expenses'),
            DB::raw('AVG(COALESCE(amount,0)) as avg_amount')
        )
            ->whereBetween('date_expense', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT));
    }

    protected function getRevenueLabel(): string
    {
        return match ($this->revenueFilter) {
            self::REVENUE_FILTER_PROMO => 'Promo Deductions',
            self::REVENUE_FILTER_ADDITIONAL => 'Additional Charges',
            self::REVENUE_FILTER_REDUCTION => 'Reductions',
            default => 'Total Revenue',
        };
    }

    protected function getExpenseOpsLabel(): string
    {
        return match ($this->expenseFilter) {
            self::EXPENSE_FILTER_COUNT => 'Number of Ops Expenses',
            self::EXPENSE_FILTER_AVG_AMOUNT => 'Average Ops Expense Amount',
            default => 'Total Expenses Ops',
        };
    }

    protected function getWeddingExpenseLabel(): string
    {
        return match ($this->expenseFilter) {
            self::EXPENSE_FILTER_COUNT => 'Number of Wedding Expenses',
            self::EXPENSE_FILTER_AVG_AMOUNT => 'Average Wedding Expense Amount',
            default => 'Total Wedding Expenses',
        };
    }

    public function getHeading(): string
    {
        $timeFrameLabel = match ($this->timeFrame) {
            'last_12_months' => 'Last 12 Months',
            'last_6_months' => 'Last 6 Months',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range',
            default => 'Selected Period',
        };

        $chartTypeLabel = $this->chartType === 'stacked' ? 'Stacked View' : 'Dual Axis View';

        return sprintf(
            'Combined Financial Overview - %s (%s)',
            $chartTypeLabel,
            $timeFrameLabel
        );
    }
}
