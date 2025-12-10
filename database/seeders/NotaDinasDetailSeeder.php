<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PengeluaranJenis;
use App\Models\NotaDinas;
use App\Models\NotaDinasDetail;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class NotaDinasDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting NotaDinasDetail seeder...');

        // Get data yang diperlukan
        $notaDinas = NotaDinas::all();
        $vendors = Vendor::where('status', 'vendor')->get();
        $orders = Order::where('status', OrderStatus::Processing)->get();

        if ($notaDinas->isEmpty()) {
            $this->command->error('No NotaDinas found! Please run NotaDinasSeeder first.');

            return;
        }

        if ($vendors->isEmpty()) {
            $this->command->error('No Vendors found! Please run VendorSeeder first.');

            return;
        }

        // Data template untuk setiap jenis pengeluaran
        $weddingData = [
            ['keperluan' => 'Dekorasi Pelaminan', 'payment_stage' => 'DP', 'jumlah_transfer' => 2500000],
            ['keperluan' => 'Catering Wedding', 'payment_stage' => 'Payment 1', 'jumlah_transfer' => 8000000],
            ['keperluan' => 'Fotografer & Videografer', 'payment_stage' => 'DP', 'jumlah_transfer' => 1500000],
            ['keperluan' => 'Makeup Artist', 'payment_stage' => 'Payment 1', 'jumlah_transfer' => 1200000],
            ['keperluan' => 'Sewa Gedung Resepsi', 'payment_stage' => 'DP', 'jumlah_transfer' => 5000000],
            ['keperluan' => 'Sound System & Lighting', 'payment_stage' => 'Payment 1', 'jumlah_transfer' => 1800000],
            ['keperluan' => 'Wedding Organizer', 'payment_stage' => 'Payment 2', 'jumlah_transfer' => 3000000],
            ['keperluan' => 'Undangan Pernikahan', 'payment_stage' => 'Final Payment', 'jumlah_transfer' => 800000],
            ['keperluan' => 'Bunga & Rangkaian', 'payment_stage' => 'DP', 'jumlah_transfer' => 1500000],
            ['keperluan' => 'Entertainment', 'payment_stage' => 'Payment 1', 'jumlah_transfer' => 2200000],
        ];

        $operasionalData = [
            ['keperluan' => 'Sewa Kantor', 'event' => 'Operasional Bulanan', 'jumlah_transfer' => 4500000],
            ['keperluan' => 'Listrik & Air', 'event' => 'Utility Kantor', 'jumlah_transfer' => 850000],
            ['keperluan' => 'Internet & Telepon', 'event' => 'Komunikasi', 'jumlah_transfer' => 650000],
            ['keperluan' => 'Supplies Kantor', 'event' => 'ATK & Perlengkapan', 'jumlah_transfer' => 750000],
            ['keperluan' => 'Maintenance Kendaraan', 'event' => 'Perawatan Operasional', 'jumlah_transfer' => 1200000],
            ['keperluan' => 'Cleaning Service', 'event' => 'Kebersihan Kantor', 'jumlah_transfer' => 800000],
            ['keperluan' => 'Security Service', 'event' => 'Keamanan Kantor', 'jumlah_transfer' => 1100000],
            ['keperluan' => 'Software License', 'event' => 'Lisensi Aplikasi', 'jumlah_transfer' => 2300000],
            ['keperluan' => 'Training Karyawan', 'event' => 'Pengembangan SDM', 'jumlah_transfer' => 1800000],
            ['keperluan' => 'Marketing & Promosi', 'event' => 'Advertising', 'jumlah_transfer' => 2500000],
        ];

        $lainLainData = [
            ['keperluan' => 'CSR Komunitas', 'event' => 'Program Sosial', 'jumlah_transfer' => 1500000],
            ['keperluan' => 'Team Building', 'event' => 'Gathering Karyawan', 'jumlah_transfer' => 2200000],
            ['keperluan' => 'Hadiah Client', 'event' => 'Apresiasi Pelanggan', 'jumlah_transfer' => 800000],
            ['keperluan' => 'Donasi Amal', 'event' => 'Kegiatan Sosial', 'jumlah_transfer' => 1200000],
            ['keperluan' => 'Penelitian Pasar', 'event' => 'Market Research', 'jumlah_transfer' => 1800000],
            ['keperluan' => 'Konsultasi Hukum', 'event' => 'Legal Advisory', 'jumlah_transfer' => 2500000],
            ['keperluan' => 'Audit Keuangan', 'event' => 'Financial Audit', 'jumlah_transfer' => 3200000],
            ['keperluan' => 'Asuransi Bisnis', 'event' => 'Business Insurance', 'jumlah_transfer' => 1800000],
            ['keperluan' => 'Seminar & Workshop', 'event' => 'Knowledge Sharing', 'jumlah_transfer' => 1500000],
            ['keperluan' => 'Emergency Fund', 'event' => 'Dana Darurat', 'jumlah_transfer' => 2000000],
        ];

        $invoiceStatuses = ['belum_dibayar', 'menunggu', 'sudah_dibayar'];
        $createdCount = 0;

        // 1. Wedding Details (10 records)
        $this->command->info('Creating Wedding expense details...');
        foreach ($weddingData as $index => $data) {
            $vendor = $vendors->random();
            $notaDinasItem = $notaDinas->random();
            $order = $orders->isNotEmpty() ? $orders->random() : null;

            try {
                NotaDinasDetail::firstOrCreate(
                    ['invoice_number' => 'INV-W-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                    [
                        'nota_dinas_id' => $notaDinasItem->id,
                        'vendor_id' => $vendor->id,
                        'account_holder' => $vendor->account_holder,
                        'bank_name' => $vendor->bank_name,
                        'bank_account' => $vendor->bank_account,
                        'keperluan' => $data['keperluan'],
                        'jenis_pengeluaran' => PengeluaranJenis::WEDDING->value,
                        'payment_stage' => $data['payment_stage'],
                        'order_id' => $order?->id,
                        'jumlah_transfer' => $data['jumlah_transfer'],
                        'status_invoice' => fake()->randomElement($invoiceStatuses),
                        'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                        'updated_at' => now(),
                    ]
                );
                $createdCount++;
            } catch (\Exception $e) {
                $this->command->warn('Failed to create wedding detail: '.$e->getMessage());
            }
        }

        // 2. Operasional Details (10 records)
        $this->command->info('Creating Operasional expense details...');
        foreach ($operasionalData as $index => $data) {
            $vendor = $vendors->random();
            $notaDinasItem = $notaDinas->random();

            try {
                NotaDinasDetail::firstOrCreate(
                    ['invoice_number' => 'INV-O-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                    [
                        'nota_dinas_id' => $notaDinasItem->id,
                        'vendor_id' => $vendor->id,
                        'account_holder' => $vendor->account_holder,
                        'bank_name' => $vendor->bank_name,
                        'bank_account' => $vendor->bank_account,
                        'keperluan' => $data['keperluan'],
                        'jenis_pengeluaran' => PengeluaranJenis::OPERASIONAL->value,
                        'event' => $data['event'],
                        'jumlah_transfer' => $data['jumlah_transfer'],
                        'status_invoice' => fake()->randomElement($invoiceStatuses),
                        'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                        'updated_at' => now(),
                    ]
                );
                $createdCount++;
            } catch (\Exception $e) {
                $this->command->warn('Failed to create operasional detail: '.$e->getMessage());
            }
        }

        // 3. Lain-lain Details (10 records)
        $this->command->info('Creating Lain-lain expense details...');
        foreach ($lainLainData as $index => $data) {
            $vendor = $vendors->random();
            $notaDinasItem = $notaDinas->random();

            try {
                NotaDinasDetail::firstOrCreate(
                    ['invoice_number' => 'INV-L-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                    [
                        'nota_dinas_id' => $notaDinasItem->id,
                        'vendor_id' => $vendor->id,
                        'account_holder' => $vendor->account_holder,
                        'bank_name' => $vendor->bank_name,
                        'bank_account' => $vendor->bank_account,
                        'keperluan' => $data['keperluan'],
                        'jenis_pengeluaran' => PengeluaranJenis::LAIN_LAIN->value,
                        'event' => $data['event'],
                        'jumlah_transfer' => $data['jumlah_transfer'],
                        'status_invoice' => fake()->randomElement($invoiceStatuses),
                        'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                        'updated_at' => now(),
                    ]
                );
                $createdCount++;
            } catch (\Exception $e) {
                $this->command->warn('Failed to create lain-lain detail: '.$e->getMessage());
            }
        }

        // Summary
        $this->command->info("Successfully created {$createdCount} NotaDinasDetail records:");
        $this->command->info('- Wedding: 10 records');
        $this->command->info('- Operasional: 10 records');
        $this->command->info('- Lain-lain: 10 records');
        $this->command->info('Total: 30 records');

        // Statistics
        $weddingCount = NotaDinasDetail::where('jenis_pengeluaran', PengeluaranJenis::WEDDING->value)->count();
        $operasionalCount = NotaDinasDetail::where('jenis_pengeluaran', PengeluaranJenis::OPERASIONAL->value)->count();
        $lainLainCount = NotaDinasDetail::where('jenis_pengeluaran', PengeluaranJenis::LAIN_LAIN->value)->count();

        $this->command->table(
            ['Jenis Pengeluaran', 'Total Records', 'Total Amount'],
            [
                [
                    'Wedding',
                    $weddingCount,
                    'Rp '.number_format(NotaDinasDetail::where('jenis_pengeluaran', PengeluaranJenis::WEDDING->value)->sum('jumlah_transfer'), 0, ',', '.'),
                ],
                [
                    'Operasional',
                    $operasionalCount,
                    'Rp '.number_format(NotaDinasDetail::where('jenis_pengeluaran', PengeluaranJenis::OPERASIONAL->value)->sum('jumlah_transfer'), 0, ',', '.'),
                ],
                [
                    'Lain-lain',
                    $lainLainCount,
                    'Rp '.number_format(NotaDinasDetail::where('jenis_pengeluaran', PengeluaranJenis::LAIN_LAIN->value)->sum('jumlah_transfer'), 0, ',', '.'),
                ],
                [
                    'TOTAL',
                    $weddingCount + $operasionalCount + $lainLainCount,
                    'Rp '.number_format(NotaDinasDetail::sum('jumlah_transfer'), 0, ',', '.'),
                ],
            ]
        );
    }
}
