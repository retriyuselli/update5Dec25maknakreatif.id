<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class FixedAssetChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // Fixed Asset Accounts (15xx series)
            [
                'account_code' => '1500',
                'account_name' => 'Tanah',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Tanah untuk operasional bisnis',
            ],
            [
                'account_code' => '1510',
                'account_name' => 'Gedung dan Bangunan',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Gedung dan bangunan untuk operasional',
            ],
            [
                'account_code' => '1520',
                'account_name' => 'Peralatan Kantor',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Peralatan kantor dan furniture',
            ],
            [
                'account_code' => '1530',
                'account_name' => 'Kendaraan',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Kendaraan operasional perusahaan',
            ],
            [
                'account_code' => '1540',
                'account_name' => 'Peralatan Komputer',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Komputer, laptop, dan peralatan IT',
            ],
            [
                'account_code' => '1550',
                'account_name' => 'Mesin dan Peralatan',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Mesin dan peralatan produksi',
            ],
            [
                'account_code' => '1560',
                'account_name' => 'Peralatan Audio Visual',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Sound system, lighting, dan peralatan wedding',
            ],

            // Accumulated Depreciation Accounts (16xx series)
            [
                'account_code' => '1610',
                'account_name' => 'Akumulasi Penyusutan Gedung',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Akumulasi penyusutan gedung dan bangunan',
            ],
            [
                'account_code' => '1620',
                'account_name' => 'Akumulasi Penyusutan Peralatan Kantor',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Akumulasi penyusutan peralatan kantor',
            ],
            [
                'account_code' => '1630',
                'account_name' => 'Akumulasi Penyusutan Kendaraan',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Akumulasi penyusutan kendaraan',
            ],
            [
                'account_code' => '1640',
                'account_name' => 'Akumulasi Penyusutan Peralatan Komputer',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Akumulasi penyusutan peralatan komputer',
            ],
            [
                'account_code' => '1650',
                'account_name' => 'Akumulasi Penyusutan Mesin',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Akumulasi penyusutan mesin dan peralatan',
            ],
            [
                'account_code' => '1660',
                'account_name' => 'Akumulasi Penyusutan Peralatan AV',
                'account_type' => 'HARTA',
                'is_active' => true,
                'description' => 'Akumulasi penyusutan peralatan audio visual',
            ],

            // Additional expense accounts if needed
            [
                'account_code' => '6209',
                'account_name' => 'Beban Penyusutan Gedung',
                'account_type' => 'BEBAN_OPERASIONAL',
                'is_active' => true,
                'description' => 'Beban penyusutan gedung dan bangunan',
            ],
            [
                'account_code' => '6210',
                'account_name' => 'Beban Penyusutan Peralatan',
                'account_type' => 'BEBAN_OPERASIONAL',
                'is_active' => true,
                'description' => 'Beban penyusutan peralatan dan kendaraan',
            ],

            // Gain/Loss on asset disposal
            [
                'account_code' => '7100',
                'account_name' => 'Laba Penjualan Aset Tetap',
                'account_type' => 'PENDAPATAN_LAIN',
                'is_active' => true,
                'description' => 'Laba dari penjualan aset tetap',
            ],
            [
                'account_code' => '7200',
                'account_name' => 'Rugi Penjualan Aset Tetap',
                'account_type' => 'BEBAN_LAIN',
                'is_active' => true,
                'description' => 'Rugi dari penjualan aset tetap',
            ],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(
                ['account_code' => $account['account_code']],
                $account
            );
        }

        $this->command->info('Fixed Asset Chart of Accounts created successfully!');
    }
}
