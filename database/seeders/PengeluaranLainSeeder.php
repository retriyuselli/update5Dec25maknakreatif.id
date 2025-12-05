<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\PengeluaranLain;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PengeluaranLainSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Starting PengeluaranLainSeeder...');

        // Optional: Clean previous seeder data
        $this->cleanPreviousSeederData();

        // Get payment methods
        $paymentMethods = PaymentMethod::all();

        if ($paymentMethods->isEmpty()) {
            $this->command->error('No payment methods found. Please run PaymentMethodSeeder first.');

            return;
        }

        // Pengeluaran lain (non-operational expenses) untuk bisnis wedding
        $expenseTypes = [
            'Investasi Peralatan Photography',
            'Pembelian Kamera DSLR Baru',
            'Upgrade Laptop Editing',
            'Pembelian Drone untuk Aerial Shot',
            'Investasi Sound System Professional',
            'Pembelian Lighting Equipment',
            'Kursus Photography & Videography',
            'Training Wedding Planning',
            'Sertifikasi Event Organizer',
            'Workshop Make-up Artist',
            'Biaya Notaris & Legal',
            'Perpanjangan Izin Usaha',
            'Biaya Trademark & Brand',
            'Konsultasi Hukum Bisnis',
            'Renovasi Studio Photography',
            'Dekorasi Showroom Wedding',
            'Furniture Kantor Baru',
            'Renovasi Meeting Room',
            'Investasi Properti Gedung',
            'Down Payment Kendaraan Operasional',
            'Cicilan Mobil Wedding Car',
            'Asuransi Comprehensive Kendaraan',
            'Modal Usaha Expansion',
            'Franchise Fee Wedding Venue',
            'Partnership Investment',
            'Research & Development',
            'Market Research Wedding Trend',
            'Konsultan Business Development',
            'Emergency Fund Allocation',
            'Bonus Karyawan Tahunan',
            'THR (Tunjangan Hari Raya)',
            'Medical Check-up Karyawan',
            'Team Building & Retreat',
            'Appreciation Event',
            'Christmas Bonus',
            'Charity & Social Responsibility',
            'Donasi Wedding Amal',
            'Sponsorship Event Komunitas',
            'CSR Program',
            'Technology Investment',
            'Software Professional License',
            'Cloud Storage Premium',
            'Website Development',
            'Mobile App Development',
            'Digital Marketing Tools',
            'CRM System Implementation',
            'Backup Server Investment',
            'Security System Upgrade',
            'Professional Services',
            'Audit Keuangan Tahunan',
        ];

        // Create 50 pengeluaran lain records
        for ($i = 1; $i <= 50; $i++) {
            // Generate dates (all in 2025)
            $expenseDate = Carbon::create(2025, rand(1, 7), rand(1, 28))->addHours(rand(8, 18))->addMinutes(rand(0, 59));

            // Random expense type
            $expenseName = $expenseTypes[array_rand($expenseTypes)];

            // Add variation to expense names
            if (rand(1, 4) === 1) {
                $variations = [
                    ' - Periode '.rand(1, 12).'/2025',
                    ' (Batch '.rand(1, 5).')',
                    ' - Premium Package',
                    ' - Tahap '.rand(1, 3),
                    ' (Upgrade)',
                ];
                $expenseName .= $variations[array_rand($variations)];
            }

            // Generate amount based on expense type
            $amount = $this->generateAmount($expenseName);

            // Generate nota dinas number
            $noNd = rand(1000, 9999);

            // Generate realistic notes
            $note = $this->generateNote($expenseName, $amount);

            PengeluaranLain::create([
                'name' => $expenseName,
                'amount' => $amount,
                'payment_method_id' => $paymentMethods->random()->id,
                'date_expense' => $expenseDate,
                'kategori_transaksi' => 'uang_keluar',
                'no_nd' => $noNd,
                'note' => $note,
                'image' => 'doc_kontrak/contoh/Contoh Invoice.jpg', // Use existing invoice image
                'created_at' => $expenseDate,
                'updated_at' => $expenseDate,
            ]);
        }

        $this->command->info('50 other expenses (pengeluaran lain) created successfully!');
    }

    /**
     * Generate realistic amount based on expense type
     */
    private function generateAmount(string $expenseName): int
    {
        $name = strtolower($expenseName);

        // Investment & Equipment (High value)
        if (str_contains($name, 'investasi') || str_contains($name, 'kamera') || str_contains($name, 'drone') || str_contains($name, 'sound system')) {
            return rand(10000000, 50000000); // 10M - 50M for equipment investment
        }

        // Property & Renovation (Very high value)
        elseif (str_contains($name, 'properti') || str_contains($name, 'renovasi') || str_contains($name, 'gedung')) {
            return rand(25000000, 100000000); // 25M - 100M for property/renovation
        }

        // Vehicle & Transportation (High value)
        elseif (str_contains($name, 'kendaraan') || str_contains($name, 'mobil') || str_contains($name, 'down payment')) {
            return rand(20000000, 80000000); // 20M - 80M for vehicles
        }

        // Training & Education (Medium value)
        elseif (str_contains($name, 'kursus') || str_contains($name, 'training') || str_contains($name, 'workshop') || str_contains($name, 'sertifikasi')) {
            return rand(2000000, 10000000); // 2M - 10M for training
        }

        // Legal & Professional Services (Medium value)
        elseif (str_contains($name, 'notaris') || str_contains($name, 'legal') || str_contains($name, 'konsultan') || str_contains($name, 'audit')) {
            return rand(3000000, 15000000); // 3M - 15M for professional services
        }

        // Technology & Software (Medium-High value)
        elseif (str_contains($name, 'software') || str_contains($name, 'website') || str_contains($name, 'app') || str_contains($name, 'technology')) {
            return rand(5000000, 25000000); // 5M - 25M for technology
        }

        // Employee Benefits & Bonus (Variable)
        elseif (str_contains($name, 'bonus') || str_contains($name, 'thr') || str_contains($name, 'medical') || str_contains($name, 'team building')) {
            return rand(3000000, 20000000); // 3M - 20M for employee benefits
        }

        // Research & Development (Medium value)
        elseif (str_contains($name, 'research') || str_contains($name, 'development') || str_contains($name, 'market research')) {
            return rand(2000000, 12000000); // 2M - 12M for R&D
        }

        // Marketing & Franchise (High value)
        elseif (str_contains($name, 'franchise') || str_contains($name, 'partnership') || str_contains($name, 'modal usaha')) {
            return rand(15000000, 75000000); // 15M - 75M for business expansion
        }

        // Charity & CSR (Low-Medium value)
        elseif (str_contains($name, 'charity') || str_contains($name, 'donasi') || str_contains($name, 'csr') || str_contains($name, 'sponsorship')) {
            return rand(1000000, 8000000); // 1M - 8M for charity/CSR
        }

        // Default range for other expenses
        else {
            return rand(2000000, 15000000); // 2M - 15M default
        }
    }

    /**
     * Generate realistic notes based on expense type
     */
    private function generateNote(string $expenseName, int $amount): string
    {
        $name = strtolower($expenseName);

        $baseNotes = [
            'investasi' => 'Investasi jangka panjang untuk meningkatkan kualitas layanan wedding organizer dan memperluas kapasitas operasional.',
            'kamera' => 'Pembelian peralatan photography profesional untuk meningkatkan kualitas dokumentasi wedding dan pre-wedding.',
            'renovasi' => 'Biaya renovasi dan upgrade fasilitas untuk menciptakan environment yang lebih professional dan nyaman bagi klien.',
            'kendaraan' => 'Investasi kendaraan operasional untuk mendukung mobilitas tim dan transportasi peralatan wedding.',
            'training' => 'Pengembangan SDM melalui pelatihan profesional untuk meningkatkan skill dan sertifikasi tim wedding.',
            'software' => 'Investasi teknologi dan software professional untuk meningkatkan efisiensi operasional dan kualitas output.',
            'legal' => 'Biaya layanan profesional untuk memastikan compliance hukum dan perlindungan bisnis wedding organizer.',
            'bonus' => 'Apresiasi dan reward untuk karyawan sebagai bentuk pengakuan atas kontribusi dan kinerja yang baik.',
            'research' => 'Riset dan pengembangan untuk mengidentifikasi tren wedding terbaru dan peluang bisnis baru.',
            'franchise' => 'Investasi ekspansi bisnis melalui kemitraan strategis dan pengembangan jaringan wedding venue.',
            'charity' => 'Program tanggung jawab sosial perusahaan sebagai kontribusi positif kepada masyarakat dan komunitas.',
        ];

        // Find matching note based on expense name
        foreach ($baseNotes as $key => $note) {
            if (str_contains($name, $key)) {
                return $note.' Total investasi: Rp '.number_format($amount, 0, ',', '.').'. Diharapkan dapat memberikan ROI positif untuk perkembangan bisnis wedding organizer.';
            }
        }

        // Default note
        return 'Pengeluaran strategis untuk '.strtolower($expenseName).'. Merupakan investasi penting yang mendukung pertumbuhan dan sustainability bisnis wedding organizer. Total alokasi dana: Rp '.number_format($amount, 0, ',', '.').'.';
    }

    /**
     * Clean previous seeder data to avoid duplicates
     */
    private function cleanPreviousSeederData(): void
    {
        // Find pengeluaran lain created by seeder
        $seederExpenses = PengeluaranLain::withTrashed()->get();

        if ($seederExpenses->count() > 0) {
            $this->command->warn("Found {$seederExpenses->count()} previous pengeluaran lain records (including soft deleted).");

            if ($this->command->confirm('Do you want to permanently delete previous PengeluaranLain seeder data before creating new ones?', true)) {
                $this->command->info('Permanently cleaning previous PengeluaranLainSeeder data...');

                // Force delete all records
                PengeluaranLain::withTrashed()->forceDelete();

                $this->command->info('Previous PengeluaranLain seeder data permanently cleaned!');
            } else {
                $this->command->info('Keeping existing data. New expenses will be added.');
            }
        } else {
            $this->command->info('No previous PengeluaranLain seeder data found.');
        }
    }
}
