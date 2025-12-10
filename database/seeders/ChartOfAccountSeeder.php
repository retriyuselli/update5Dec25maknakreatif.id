<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Seeding Chart of Accounts...');

        $create = function (array $data) {
            $code = $data['account_code'];
            $existing = ChartOfAccount::withTrashed()->where('account_code', $code)->first();
            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->update($data);
                return $existing;
            }

            return ChartOfAccount::create($data);
        };

        // Level 1: HARTA (Assets)
        $assets = $create([
            'account_code' => '110000000',
            'account_name' => 'Harta',
            'account_type' => 'HARTA',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Akun induk untuk semua aset',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '111000000',
            'account_name' => 'Kas',
            'account_type' => 'HARTA',
            'parent_id' => $assets->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Kas kecil dan kas operasional',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '112000000',
            'account_name' => 'Bank',
            'account_type' => 'HARTA',
            'parent_id' => $assets->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Saldo rekening bank perusahaan',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '113000000',
            'account_name' => 'Piutang Usaha',
            'account_type' => 'HARTA',
            'parent_id' => $assets->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Tagihan ke pelanggan',
            'normal_balance' => 'debit',
        ]);

        $fixedAssets = $create([
            'account_code' => '115000000',
            'account_name' => 'Aset Tetap',
            'account_type' => 'HARTA',
            'parent_id' => $assets->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Aset tetap seperti peralatan, kendaraan',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '115100000',
            'account_name' => 'Aset Tetap - Peralatan',
            'account_type' => 'HARTA',
            'parent_id' => $fixedAssets->id,
            'level' => 3,
            'is_active' => true,
            'description' => 'Peralatan kantor dan operasional',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '115900000',
            'account_name' => 'Akumulasi Penyusutan Aset Tetap',
            'account_type' => 'HARTA',
            'parent_id' => $fixedAssets->id,
            'level' => 3,
            'is_active' => true,
            'description' => 'Akumulasi penyusutan aset tetap (akun kontra)',
            'normal_balance' => 'credit',
        ]);

        // Level 1: KEWAJIBAN (Liabilities)
        $liabilities = $create([
            'account_code' => '210000000',
            'account_name' => 'Kewajiban',
            'account_type' => 'KEWAJIBAN',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Akun induk untuk semua kewajiban',
            'normal_balance' => 'credit',
        ]);

        $create([
            'account_code' => '211000000',
            'account_name' => 'Hutang Usaha',
            'account_type' => 'KEWAJIBAN',
            'parent_id' => $liabilities->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Kewajiban kepada vendor/mitra',
            'normal_balance' => 'credit',
        ]);

        $create([
            'account_code' => '212000000',
            'account_name' => 'Hutang Bank',
            'account_type' => 'KEWAJIBAN',
            'parent_id' => $liabilities->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Pinjaman bank',
            'normal_balance' => 'credit',
        ]);

        // Level 1: MODAL (Equity)
        $equity = $create([
            'account_code' => '310000000',
            'account_name' => 'Modal',
            'account_type' => 'MODAL',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Akun induk untuk modal dan laba ditahan',
            'normal_balance' => 'credit',
        ]);

        $create([
            'account_code' => '311000000',
            'account_name' => 'Modal Disetor',
            'account_type' => 'MODAL',
            'parent_id' => $equity->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Modal yang disetor pemilik',
            'normal_balance' => 'credit',
        ]);

        $create([
            'account_code' => '312000000',
            'account_name' => 'Laba Ditahan',
            'account_type' => 'MODAL',
            'parent_id' => $equity->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Akumulasi laba/rugi tahun berjalan',
            'normal_balance' => 'credit',
        ]);

        // Level 1: PENDAPATAN (Revenue)
        $revenue = $create([
            'account_code' => '410000000',
            'account_name' => 'Pendapatan',
            'account_type' => 'PENDAPATAN',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Akun induk untuk pendapatan utama',
            'normal_balance' => 'credit',
        ]);

        $create([
            'account_code' => '411000000',
            'account_name' => 'Pendapatan Proyek',
            'account_type' => 'PENDAPATAN',
            'parent_id' => $revenue->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Pendapatan dari proyek wedding organizer',
            'normal_balance' => 'credit',
        ]);

        // Level 1: BEBAN ATAS PENDAPATAN (COGS)
        $cogs = $create([
            'account_code' => '510000000',
            'account_name' => 'Beban Atas Pendapatan',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Biaya langsung terkait pendapatan (vendor, venue)',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '511000000',
            'account_name' => 'Biaya Vendor',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'parent_id' => $cogs->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Pembayaran vendor acara',
            'normal_balance' => 'debit',
        ]);

        // Level 1: BEBAN OPERASIONAL (Operating Expenses)
        $opex = $create([
            'account_code' => '610000000',
            'account_name' => 'Beban Operasional',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Biaya operasional perusahaan',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '611000000',
            'account_name' => 'Gaji & Tunjangan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $opex->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Gaji karyawan dan tunjangan',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '612000000',
            'account_name' => 'Sewa Kantor',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $opex->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Biaya sewa kantor',
            'normal_balance' => 'debit',
        ]);

        // Level 1: PENDAPATAN LAIN (Other Income)
        $otherIncome = $create([
            'account_code' => '810000000',
            'account_name' => 'Pendapatan Lain',
            'account_type' => 'PENDAPATAN_LAIN',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Pendapatan di luar usaha utama',
            'normal_balance' => 'credit',
        ]);

        $create([
            'account_code' => '811000000',
            'account_name' => 'Pendapatan Bunga',
            'account_type' => 'PENDAPATAN_LAIN',
            'parent_id' => $otherIncome->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Pendapatan bunga bank',
            'normal_balance' => 'credit',
        ]);

        // Level 1: BEBAN LAIN (Other Expenses)
        $otherExpenses = $create([
            'account_code' => '910000000',
            'account_name' => 'Beban Lain',
            'account_type' => 'BEBAN_LAIN',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'description' => 'Beban di luar usaha utama',
            'normal_balance' => 'debit',
        ]);

        $create([
            'account_code' => '911000000',
            'account_name' => 'Kerugian Penjualan Aset',
            'account_type' => 'BEBAN_LAIN',
            'parent_id' => $otherExpenses->id,
            'level' => 2,
            'is_active' => true,
            'description' => 'Kerugian dari pelepasan aset tetap',
            'normal_balance' => 'debit',
        ]);

        $this->command?->info('Chart of Accounts seeding completed.');
    }
}

