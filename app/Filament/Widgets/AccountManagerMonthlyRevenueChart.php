<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Forms\Components\Select; // Meskipun tidak langsung di-query, baik untuk konteks
use Filament\Schemas\Components\Grid;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AccountManagerMonthlyRevenueChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 23;

    public ?string $selectedAccountId = null;

    public ?string $selectedMonth = null;

    public function getHeading(): string
    {
        $heading = 'Jumlah Revenue Semua Account Manager';
        if ($this->selectedAccountId && $this->selectedAccountId !== 'all') {
            $user = User::find($this->selectedAccountId);
            $heading .= ' for: '.($user ? $user->name : 'Semua Account Manager');
        } else {
            $heading .= ' Per Bulan';
        }

        if ($this->selectedMonth && $this->selectedMonth !== 'all') {
            $monthName = Carbon::create()->month((int) $this->selectedMonth)->format('F');
            $heading .= ' - '.$monthName.' '.Carbon::now()->year;
        } else {
            $heading .= '';
        }

        return $heading;
    }

    protected function getFormSchema(): array
    {
        $accountManagers = User::role('Account Manager')->pluck('name', 'id')->toArray();
        $options = ['all' => 'Semua Account Manager'] + $accountManagers;

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[str_pad($m, 2, '0', STR_PAD_LEFT)] = Carbon::create()->month($m)->format('F');
        }
        $optionsMonth = ['all' => 'All Months (Last 13)'] + $months;

        return [
            Grid::make(2)
                ->schema([
                    Select::make('selectedAccountId')
                        ->label('Pilih Account Manager')
                        ->options($options)
                        ->default('all')
                        ->live()
                        ->afterStateUpdated(fn () => $this->selectedAccountId = $this->filterFormData['selectedAccountId'] ?? 'all'),
                    Select::make('selectedMonth')
                        ->label('Pilih Bulan (Tahun Ini)')
                        ->options($optionsMonth)
                        ->default('all')
                        ->live()
                        ->afterStateUpdated(fn () => $this->selectedMonth = $this->filterFormData['selectedMonth'] ?? 'all'),
                ]),
        ];
    }

    protected function getData(): array
    {
        // Mengambil data order yang di-handle oleh user dengan peran 'Account Manager'
        // dan menjumlahkan 'total_price' per bulan berdasarkan 'closing_date'

        $endDate = Carbon::now()->endOfMonth(); // Akhir bulan ini
        $startDate = Carbon::now()->subMonths(12)->startOfMonth(); // Default 13 bulan terakhir (termasuk bulan ini)

        if ($this->selectedMonth && $this->selectedMonth !== 'all') {
            // Jika bulan spesifik dipilih, gunakan bulan tersebut di tahun ini
            $currentYear = Carbon::now()->year;
            $month = (int) $this->selectedMonth;
            $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();
        }

        $query = Order::query()
            ->whereNotNull('closing_date') // Pastikan tanggal closing ada
            ->whereBetween('closing_date', [$startDate, $endDate]) // Filter berdasarkan rentang tanggal
            ->whereHas('user', function (Builder $query) {
                // Pastikan nama peran 'Account Manager' sesuai dengan yang ada di database Anda
                $query->role('Account Manager');

                // Terapkan filter jika Account Manager spesifik dipilih
                if ($this->selectedAccountId && $this->selectedAccountId !== 'all') {
                    $query->where('users.id', $this->selectedAccountId);
                }
            });

        $monthlyRevenueData = $query->select(
            DB::raw('YEAR(closing_date) as year'),
            DB::raw('MONTH(closing_date) as month'),
            DB::raw('SUM(total_price) as total_revenue') // Menjumlahkan total_price
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare labels and data for all months in the range
        $labels = [];
        $data = [];
        $currentLoopDate = $startDate->copy();
        $revenueDataMap = $monthlyRevenueData->keyBy(function ($item) {
            return Carbon::createFromDate($item->year, $item->month, 1)->format('Y-m');
        });

        while ($currentLoopDate <= $endDate) {
            $labels[] = $currentLoopDate->format('M Y');
            $yearMonthKey = $currentLoopDate->format('Y-m');
            $data[] = $revenueDataMap->get($yearMonthKey)->total_revenue ?? 0;
            $currentLoopDate->addMonthNoOverflow();
        }

        // $labels = $monthlyRevenueData->map(function ($item) { ... })->toArray(); // Old way
        // $data = $monthlyRevenueData->pluck('total_revenue')->toArray(); // Old way

        return [
            'datasets' => [
                [
                    'label' => 'Total Revenue (Closings)',
                    'data' => $data,
                    'borderColor' => 'rgb(75, 192, 192)', // Warna Teal
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Anda bisa ganti ke 'bar' jika lebih suka
    }
}
