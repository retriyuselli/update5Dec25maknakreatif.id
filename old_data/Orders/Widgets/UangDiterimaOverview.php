<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Enums\OrderStatus;
use App\Models\DataPembayaran;
use App\Models\Expense;
use App\Models\Order;
use BackedEnum; // Pastikan enum ini ada dan sesuai
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class UangDiterimaOverview extends BaseWidget
{
    protected static ?int $sort = -3; // Urutan widget, sesuaikan jika perlu

    protected function getStats(): array
    {
        // Anda bisa membuat status target dinamis jika diperlukan,
        // misalnya melalui properti widget atau filter global.
        // Untuk contoh ini, kita gunakan status 'processing'.
        $statusTarget = OrderStatus::Processing; // Ganti dengan OrderStatus::DONE jika ingin status 'done'

        $targetOrderIds = Order::where('status', $statusTarget)->pluck('id');

        $totalPembayaranUntukTargetOrder = DataPembayaran::whereIn('order_id', $targetOrderIds)
            // SoftDeletes pada DataPembayaran sudah otomatis ditangani Eloquent
            ->sum('nominal');

        $totalPengeluaranUntukTargetOrder = Expense::whereIn('order_id', $targetOrderIds)->sum('amount');

        $sumUangDiterimaUntukTargetOrder = $totalPembayaranUntukTargetOrder - $totalPengeluaranUntukTargetOrder;

        // Deskripsi bisa disesuaikan berdasarkan statusTarget
        $descriptionText = 'Untuk order dengan status '.($statusTarget instanceof BackedEnum ? $statusTarget->value : $statusTarget);

        return [
            Stat::make('Total Uang Diterima ('.($statusTarget instanceof BackedEnum ? $statusTarget->value : $statusTarget).')', 'Rp '.Number::format($sumUangDiterimaUntukTargetOrder, precision: 0, locale: 'id'))
                ->description($descriptionText)
                ->descriptionIcon('heroicon-m-banknotes') // Ganti ikon jika perlu
                ->color('primary'), // Ganti warna jika perlu (success, warning, danger, etc.)
        ];
    }
}
