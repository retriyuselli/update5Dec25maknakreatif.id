<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorPriceHistory;
use Illuminate\Database\Seeder;

class VendorPriceHistorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Vendor Price History...');

        if (! \Illuminate\Support\Facades\Schema::hasTable('vendor_price_histories')) {
            $this->command->warn('Table vendor_price_histories not found. Skipping VendorPriceHistorySeeder.');
            return;
        }

        $vendors = Vendor::all();
        if ($vendors->isEmpty()) {
            $this->command->error('No Vendor found. Please run VendorSeeder first.');
            return;
        }

        foreach ($vendors->take(5) as $vendor) {
            $publish = (float) $vendor->harga_publish;
            $vendorPrice = (float) $vendor->harga_vendor;
            $profit = $publish - $vendorPrice;
            $margin = $vendorPrice > 0 ? ($profit / $vendorPrice) * 100 : 0;

            VendorPriceHistory::firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'effective_from' => now()->subMonths(3)->startOfDay(),
                ],
                [
                    'harga_publish' => $publish,
                    'harga_vendor' => $vendorPrice,
                    'profit_amount' => $profit,
                    'profit_margin' => $margin,
                    'effective_to' => now()->subMonth()->startOfDay(),
                    'status' => 'inactive',
                ]
            );

            // Ensure no other active records exist to prevent validation error
            VendorPriceHistory::where('vendor_id', $vendor->id)->update(['status' => 'inactive']);

            VendorPriceHistory::firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'effective_from' => now()->subMonth()->startOfDay(),
                ],
                [
                    'harga_publish' => $publish * 1.05,
                    'harga_vendor' => $vendorPrice * 1.04,
                    'profit_amount' => ($publish * 1.05) - ($vendorPrice * 1.04),
                    'profit_margin' => (($publish * 1.05) - ($vendorPrice * 1.04)) / max(1, ($vendorPrice * 1.04)) * 100,
                    'effective_to' => null,
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Vendor price history seeded.');
    }
}
