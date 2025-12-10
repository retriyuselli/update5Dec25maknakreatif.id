<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\FixedAsset;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FixedAssetSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Fixed Assets...');

        $assetAccounts = [
            'EQUIPMENT' => ChartOfAccount::where('account_code', '1520')->first(),
            'VEHICLE' => ChartOfAccount::where('account_code', '1530')->first(),
            'COMPUTER' => ChartOfAccount::where('account_code', '1540')->first(),
        ];

        $accumAccounts = [
            'EQUIPMENT' => ChartOfAccount::where('account_code', '1620')->first(),
            'VEHICLE' => ChartOfAccount::where('account_code', '1630')->first(),
            'COMPUTER' => ChartOfAccount::where('account_code', '1640')->first(),
        ];

        if (! $assetAccounts['EQUIPMENT'] || ! $assetAccounts['VEHICLE'] || ! $assetAccounts['COMPUTER']) {
            $this->command->error('Fixed Asset accounts not found. Please run FixedAssetChartOfAccountsSeeder first.');
            return;
        }

        $data = [
            [
                'category' => 'EQUIPMENT',
                'asset_name' => 'Peralatan Kantor - Set Meja & Kursi',
                'purchase_date' => Carbon::parse('2024-01-15'),
                'purchase_price' => 25000000,
                'salvage_value' => 3000000,
                'useful_life_years' => 5,
                'useful_life_months' => 0,
                'location' => 'Kantor Pusat Jakarta',
                'condition' => 'GOOD',
                'supplier' => 'PT Furni Jaya',
                'invoice_number' => 'INV-FA-001',
                'warranty_expiry' => Carbon::parse('2026-01-15'),
                'notes' => null,
            ],
            [
                'category' => 'VEHICLE',
                'asset_name' => 'Kendaraan Operasional - MPV',
                'purchase_date' => Carbon::parse('2024-03-20'),
                'purchase_price' => 320000000,
                'salvage_value' => 50000000,
                'useful_life_years' => 8,
                'useful_life_months' => 0,
                'location' => 'Garasi Kantor',
                'condition' => 'GOOD',
                'supplier' => 'PT Mobil Nusantara',
                'invoice_number' => 'INV-FA-002',
                'warranty_expiry' => Carbon::parse('2027-03-20'),
                'notes' => null,
            ],
            [
                'category' => 'COMPUTER',
                'asset_name' => 'Komputer Editing - Workstation',
                'purchase_date' => Carbon::parse('2024-05-05'),
                'purchase_price' => 45000000,
                'salvage_value' => 5000000,
                'useful_life_years' => 4,
                'useful_life_months' => 0,
                'location' => 'Studio Editing',
                'condition' => 'EXCELLENT',
                'supplier' => 'PT Tekno Mandiri',
                'invoice_number' => 'INV-FA-003',
                'warranty_expiry' => Carbon::parse('2026-05-05'),
                'notes' => null,
            ],
        ];

        $created = 0;

        foreach ($data as $item) {
            $category = $item['category'];
            $assetAccount = $assetAccounts[$category];
            $accumAccount = $accumAccounts[$category];

            $asset = FixedAsset::firstOrCreate(
                [
                    'asset_name' => $item['asset_name'],
                    'purchase_date' => $item['purchase_date'],
                ],
                [
                    'asset_code' => FixedAsset::generateAssetCode($category),
                    'category' => $category,
                    'purchase_price' => $item['purchase_price'],
                    'accumulated_depreciation' => 0,
                    'depreciation_method' => 'STRAIGHT_LINE',
                    'useful_life_years' => $item['useful_life_years'],
                    'useful_life_months' => $item['useful_life_months'],
                    'salvage_value' => $item['salvage_value'],
                    'current_book_value' => $item['purchase_price'],
                    'location' => $item['location'],
                    'condition' => $item['condition'],
                    'supplier' => $item['supplier'],
                    'invoice_number' => $item['invoice_number'],
                    'warranty_expiry' => $item['warranty_expiry'],
                    'notes' => $item['notes'],
                    'chart_of_account_id' => $assetAccount->id,
                    'depreciation_account_id' => $accumAccount->id,
                    'is_active' => true,
                ]
            );

            $asset->updateBookValue();
            $created++;
            $this->command->line("- {$asset->asset_name} ({$asset->asset_code})");
        }

        $this->command->info("Created {$created} fixed assets.");
    }
}

