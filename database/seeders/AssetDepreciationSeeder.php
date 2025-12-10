<?php

namespace Database\Seeders;

use App\Models\AssetDepreciation;
use App\Models\FixedAsset;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AssetDepreciationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Asset Depreciation entries...');

        $assets = FixedAsset::all();
        if ($assets->isEmpty()) {
            $this->command->error('No fixed assets found. Please run FixedAssetSeeder first.');
            return;
        }

        $created = 0;

        foreach ($assets as $asset) {
            $monthly = $asset->calculateMonthlyDepreciation();
            if ($monthly <= 0) {
                continue;
            }

            $dates = [
                Carbon::parse('2025-01-31'),
                Carbon::parse('2025-02-28'),
                Carbon::parse('2025-03-31'),
            ];

            foreach ($dates as $date) {
                $beforeAccum = (float) $asset->accumulated_depreciation;
                $afterAccum = $beforeAccum + $monthly;
                $beforeBook = (float) $asset->current_book_value;
                $afterBook = max(0, $asset->purchase_price - $afterAccum);

                $exists = AssetDepreciation::where('fixed_asset_id', $asset->id)
                    ->whereDate('depreciation_date', $date->toDateString())
                    ->first();

                if ($exists) {
                    continue;
                }

                AssetDepreciation::create([
                    'fixed_asset_id' => $asset->id,
                    'depreciation_date' => $date,
                    'depreciation_amount' => $monthly,
                    'accumulated_depreciation_before' => $beforeAccum,
                    'accumulated_depreciation_after' => $afterAccum,
                    'book_value_before' => $beforeBook,
                    'book_value_after' => $afterBook,
                    'journal_batch_id' => null,
                    'notes' => null,
                    'is_adjustment' => false,
                ]);

                $asset->accumulated_depreciation = $afterAccum;
                $asset->updateBookValue();

                $created++;
            }
        }

        $this->command->info("Created {$created} depreciation entries.");
    }
}

