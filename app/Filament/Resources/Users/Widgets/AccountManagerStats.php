<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Storage;

// Pastikan ini diimpor jika menggunakan query kompleks

class AccountManagerStats extends BaseWidget
{
    // protected ?string $pollingInterval = null;

    // Definisikan target bulanan
    protected const MONTHLY_TARGET = 1000000000;

    protected function getStats(): array
    {
        // Ambil semua pengguna yang memiliki peran 'Account Manager'
        // Pastikan Anda memiliki peran 'Account Manager' di database Anda
        // dan pengguna telah di-assign ke peran tersebut.
        $accountManagers = User::role('Account Manager')
            ->where(function ($query) {
                // Kondisi untuk menyertakan AM yang masih aktif:
                // 1. Tidak memiliki record employee (diasumsikan aktif)
                // 2. Atau memiliki record employee dengan date_of_out null (belum keluar)
                // 3. Atau memiliki record employee dengan date_of_out hari ini atau di masa depan
                $query->whereDoesntHave('firstEmployee')
                    ->orWhereHas('firstEmployee', function ($employeeQuery) {
                        $employeeQuery->whereNull('date_of_out')
                            ->orWhereDate('date_of_out', '>=', now());
                    });
            })
            ->withSum(['orders as monthly_closing' => function ($query) {
                // Ambil closing untuk bulan ini berdasarkan closing_date.
                $query->whereMonth('closing_date', now()->month) // Menggunakan closing_date
                    ->whereYear('closing_date', now()->year); // Menggunakan closing_date
            }], 'total_price') // Sesuaikan 'total_price' dengan kolom harga di tabel Order
            ->get();

        if ($accountManagers->isEmpty()) {
            return [
                Stat::make('Account Managers', 'Tidak ada data')
                    ->description('Tidak ada pengguna dengan peran Account Manager ditemukan.')
                    ->color('danger'),
            ];
        }

        $stats = [];
        foreach ($accountManagers as $am) {
            $achievement = $am->monthly_closing ?? 0; // Pencapaian bulan ini
            $percentage = (self::MONTHLY_TARGET > 0) ? ($achievement / self::MONTHLY_TARGET) * 100 : 0;
            $percentage = round($percentage, 2);

            // Mendapatkan URL avatar
            $avatarUrl = $am->avatar_url ? Storage::url($am->avatar_url) : $am->getFilamentAvatarUrl();

            $stats[] = Stat::make($am->name, 'Rp '.number_format($achievement, 0, ',', '.'))
                ->description('Pencapaian Bulan Ini: '.$percentage.'% dari Rp '.number_format(self::MONTHLY_TARGET, 0, ',', '.'))
                ->color($achievement >= self::MONTHLY_TARGET ? 'success' : ($percentage > 50 ? 'warning' : 'danger'))
                ->icon($avatarUrl) // Menampilkan avatar
                // Anda bisa menambahkan URL ke detail pengguna jika diinginkan
                ->url(route('filament.admin.resources.users.edit', ['record' => $am]));

            // Stat tambahan untuk menampilkan sisa target
            $remainingTarget = self::MONTHLY_TARGET - $achievement;
            $stats[] = Stat::make('Sisa Target '.$am->name, 'Rp '.number_format(max(0, $remainingTarget), 0, ',', '.'))
                ->description('Menuju target bulanan')
                ->color('info');
        }

        return $stats;
    }

    /**
     * Mengatur lebar kolom widget.
     *
     * @return array<string, int | string | null> | int | string
     */
    protected function getColumns(): int
    {
        // Sesuaikan jumlah kolom berdasarkan berapa banyak AM yang ingin ditampilkan per baris
        // Misalnya, jika ada banyak AM, mungkin lebih baik 3 atau 4 kolom.
        return 3;
    }
}
