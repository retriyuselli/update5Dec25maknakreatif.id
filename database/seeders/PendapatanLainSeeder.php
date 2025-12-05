<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\PendapatanLain;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PendapatanLainSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Starting PendapatanLainSeeder...');

        // Optional: Clean previous seeder data
        $this->cleanPreviousSeederData();

        // Get payment methods
        $paymentMethods = PaymentMethod::all();

        if ($paymentMethods->isEmpty()) {
            $this->command->error('No payment methods found. Please run PaymentMethodSeeder first.');

            return;
        }

        // Pendapatan lain (non-wedding income) untuk bisnis wedding organizer
        $incomeTypes = [
            'Konsultasi Wedding Planning',
            'Workshop Photography untuk Umum',
            'Training Event Organizer',
            'Jasa Konsultasi Dekorasi',
            'Sewa Peralatan Photography',
            'Rental Sound System',
            'Jasa Editing Video Professional',
            'Konsultasi Brand Wedding Venue',
            'Workshop Make-up Artist',
            'Jasa Desain Undangan Custom',
            'Sewa Lighting Equipment',
            'Konsultasi Digital Marketing',
            'Afiliasi Commission Wedding Vendor',
            'Royalty Website Template',
            'Income dari Partnership Venue',
        ];

        // Create 15 pendapatan lain records
        for ($i = 1; $i <= 15; $i++) {
            // Generate dates (all in 2025)
            $incomeDate = Carbon::create(2025, rand(1, 7), rand(1, 28))->addHours(rand(8, 18))->addMinutes(rand(0, 59));

            // Random income type
            $incomeName = $incomeTypes[array_rand($incomeTypes)];

            // Add variation to income names
            if (rand(1, 3) === 1) {
                $variations = [
                    ' - Batch '.rand(1, 5),
                    ' (Premium Package)',
                    ' - Periode '.Carbon::create(2025, rand(1, 7))->format('F'),
                    ' (Corporate Client)',
                    ' - Special Edition',
                ];
                $incomeName .= $variations[array_rand($variations)];
            }

            // Generate amount based on income type
            $amount = $this->generateAmount($incomeName);

            // Generate realistic description
            $description = $this->generateDescription($incomeName, $amount);

            PendapatanLain::create([
                'name' => $incomeName,
                'nominal' => $amount,
                'payment_method_id' => $paymentMethods->random()->id,
                'tgl_bayar' => $incomeDate,
                'kategori_transaksi' => 'uang_masuk',
                'keterangan' => $description,
                'image' => 'doc_kontrak/contoh/Contoh Bukti Rekening.jpg', // Use existing payment proof image
                'created_at' => $incomeDate,
                'updated_at' => $incomeDate,
            ]);
        }

        $this->command->info('15 other income records (pendapatan lain) created successfully!');
    }

    /**
     * Generate realistic amount based on income type
     */
    private function generateAmount(string $incomeName): int
    {
        $name = strtolower($incomeName);

        // Consultation & Training Services (Medium-High value)
        if (str_contains($name, 'konsultasi') || str_contains($name, 'workshop') || str_contains($name, 'training')) {
            return rand(3000000, 15000000); // 3M - 15M for consultation/training
        }

        // Equipment Rental (Medium value)
        elseif (str_contains($name, 'sewa') || str_contains($name, 'rental')) {
            return rand(1000000, 8000000); // 1M - 8M for equipment rental
        }

        // Professional Services (High value)
        elseif (str_contains($name, 'jasa editing') || str_contains($name, 'jasa desain') || str_contains($name, 'digital marketing')) {
            return rand(5000000, 20000000); // 5M - 20M for professional services
        }

        // Brand & Partnership (High value)
        elseif (str_contains($name, 'brand') || str_contains($name, 'partnership') || str_contains($name, 'afiliasi')) {
            return rand(8000000, 25000000); // 8M - 25M for brand/partnership
        }

        // Royalty & Commission (Variable)
        elseif (str_contains($name, 'royalty') || str_contains($name, 'commission')) {
            return rand(2000000, 12000000); // 2M - 12M for royalty/commission
        }

        // Default range for other income
        else {
            return rand(2000000, 10000000); // 2M - 10M default
        }
    }

    /**
     * Generate realistic description based on income type
     */
    private function generateDescription(string $incomeName, int $amount): string
    {
        $name = strtolower($incomeName);

        $baseDescriptions = [
            'konsultasi' => 'Layanan konsultasi profesional untuk klien yang membutuhkan expertise wedding planning. Mencakup advice strategis, planning timeline, dan koordinasi vendor.',
            'workshop' => 'Workshop training untuk meningkatkan skill peserta di bidang wedding industry. Materi lengkap dengan praktik langsung dan sertifikat.',
            'sewa' => 'Layanan penyewaan peralatan profesional untuk kebutuhan event dan photography. Kualitas premium dengan support teknis.',
            'jasa editing' => 'Jasa editing video profesional dengan hasil cinematic quality. Termasuk color grading, audio mixing, dan motion graphics.',
            'jasa desain' => 'Layanan desain kreatif custom sesuai dengan tema dan konsep yang diinginkan klien. Revisi unlimited hingga approved.',
            'brand' => 'Kerjasama strategis dengan brand terkemuka dalam industri wedding. Mutual benefit untuk pengembangan bisnis.',
            'partnership' => 'Income dari kemitraan strategis dengan venue dan vendor wedding. Komisi berdasarkan referral dan collaboration project.',
            'afiliasi' => 'Pendapatan dari program afiliasi dengan vendor wedding terpilih. Komisi berdasarkan performa dan target achievement.',
            'royalty' => 'Royalty dari penggunaan template, design, atau intellectual property yang telah dikembangkan perusahaan.',
            'digital marketing' => 'Layanan digital marketing untuk vendor wedding yang ingin meningkatkan online presence dan customer acquisition.',
        ];

        // Find matching description based on income name
        foreach ($baseDescriptions as $key => $description) {
            if (str_contains($name, $key)) {
                return $description.' Total pendapatan yang diterima: Rp '.number_format($amount, 0, ',', '.').'. Pembayaran diterima sesuai dengan terms & conditions yang telah disepakati.';
            }
        }

        // Default description
        return 'Pendapatan tambahan dari '.strtolower($incomeName).'. Merupakan diversifikasi income stream untuk memperkuat financial stability perusahaan. Total yang diterima: Rp '.number_format($amount, 0, ',', '.').'. Pembayaran telah dikonfirmasi dan sesuai dengan agreement.';
    }

    /**
     * Clean previous seeder data to avoid duplicates
     */
    private function cleanPreviousSeederData(): void
    {
        // Find pendapatan lain created by seeder
        $seederIncomes = PendapatanLain::withTrashed()->get();

        if ($seederIncomes->count() > 0) {
            $this->command->warn("Found {$seederIncomes->count()} previous pendapatan lain records (including soft deleted).");

            if ($this->command->confirm('Do you want to permanently delete previous PendapatanLain seeder data before creating new ones?', true)) {
                $this->command->info('Permanently cleaning previous PendapatanLainSeeder data...');

                // Force delete all records
                PendapatanLain::withTrashed()->forceDelete();

                $this->command->info('Previous PendapatanLain seeder data permanently cleaned!');
            } else {
                $this->command->info('Keeping existing data. New income records will be added.');
            }
        } else {
            $this->command->info('No previous PendapatanLain seeder data found.');
        }
    }
}
