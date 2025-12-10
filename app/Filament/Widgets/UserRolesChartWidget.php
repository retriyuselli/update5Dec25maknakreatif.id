<?php

namespace App\Filament\Widgets;

use App\Models\Order; // Tambahkan ini
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB; // Pastikan DB facade di-import

class UserRolesChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 22; // Sesuaikan urutan widget di dashboard


    public ?string $selectedAccountId = null;

    public function getHeading(): string
    {
        if ($this->selectedAccountId && $this->selectedAccountId !== 'all') {
            $user = User::find($this->selectedAccountId);

            return 'Jumlah Closing per Bulan untuk: '.($user ? $user->name : 'Account Manager');
        }

        return 'Jumlah Closing Semua Account Manager per Bulan';
    }

    protected function getFormSchema(): array
    {
        $accountManagers = User::role('Account Manager')->pluck('name', 'id')->toArray();
        // Tambahkan opsi "Semua Account Manager" di awal array
        $options = ['all' => 'Semua Account Manager'] + $accountManagers;

        return [
            Select::make('selectedAccountId')
                ->label('Pilih Account Manager')
                ->options($options)
                ->default('all')
                ->live() // Agar chart di-refresh saat filter berubah
                ->afterStateUpdated(fn () => $this->selectedAccountId = $this->filterFormData['selectedAccountId'] ?? 'all'),
        ];
    }

    protected function getData(): array
    {
        $queryAccountManagerIds = [];

        if ($this->selectedAccountId && $this->selectedAccountId !== 'all') {
            $queryAccountManagerIds = [$this->selectedAccountId];
        } else {
            // Jika 'Semua Account Manager' dipilih atau filter kosong, ambil semua ID Account Manager
            $queryAccountManagerIds = User::role('Account Manager')->pluck('id')->all();
        }

        if (empty($queryAccountManagerIds)) {
            return [
                'datasets' => [[
                    'label' => 'Jumlah Closing',
                    'data' => [],
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'fill' => true,
                ]],
                'labels' => [],
            ];
        }

        // Tentukan rentang tanggal untuk 12 bulan terakhir
        $endDate = Carbon::now()->endOfMonth();
        $startDate = Carbon::now()->subMonths(12)->startOfMonth(); // 13 bulan termasuk bulan ini

        $yearMonthKeys = [];
        $finalLabels = [];
        $currentPeriod = $startDate->copy();
        while ($currentPeriod <= $endDate) {
            $finalLabels[] = $currentPeriod->format('M Y');
            $yearMonthKeys[] = $currentPeriod->format('Y-m');
            $currentPeriod->addMonth();
        }

        // 2. Ambil data closing per bulan untuk Account Manager tersebut
        // Menggunakan model Order dan kolom closing_date
        $closingsData = Order::query()
            ->whereIn('user_id', $queryAccountManagerIds)
            ->whereNotNull('closing_date') // Pastikan tanggal closing ada
            ->whereBetween('closing_date', [$startDate, $endDate]) // Filter untuk 12 bulan terakhir
            ->select(
                // Menggunakan kolom 'closing_date' dari tabel 'orders'
                DB::raw('YEAR(closing_date) as year'),
                DB::raw('MONTH(closing_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            // Key hasil query dengan format 'YYYY-MM' untuk memudahkan pemetaan
            ->keyBy(fn ($item) => $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT));

        // Siapkan data untuk chart, pastikan ada entri untuk setiap bulan dalam 12 bulan terakhir
        $data = collect($yearMonthKeys)->map(function ($yearMonthKey) use ($closingsData) {
            return $closingsData->get($yearMonthKey)->count ?? 0;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Closing',
                    'data' => $data,
                    'borderColor' => 'rgb(75, 192, 192)', // Warna Teal untuk garis
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)', // Warna Teal transparan untuk area
                    'fill' => true, // Mengisi area di bawah garis
                ],
            ],
            'labels' => $finalLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
