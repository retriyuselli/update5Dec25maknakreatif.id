<?php

namespace Database\Seeders;

use App\Models\ExpenseOps;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExpenseOpsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Starting ExpenseOpsSeeder...');

        // Optional: Clean previous seeder data
        $this->cleanPreviousSeederData();

        // Get payment methods
        $paymentMethods = PaymentMethod::all();

        if ($paymentMethods->isEmpty()) {
            $this->command->error('No payment methods found. Please run PaymentMethodSeeder first.');

            return;
        }

        // Common operational expenses for wedding business
        $expenseTypes = [
            'Listrik Kantor',
            'Internet & Telepon',
            'ATK (Alat Tulis Kantor)',
            'Transportasi',
            'Konsumsi Meeting',
            'Maintenance Komputer',
            'Fotokopi & Printing',
            'Bensin Kendaraan',
            'Parkir',
            'Pulsa & Data',
            'Kebersihan Kantor',
            'Asuransi Kendaraan',
            'Pajak Kendaraan',
            'Service AC',
            'Maintenance Website',
            'Software License',
            'Domain & Hosting',
            'Marketing Material',
            'Brosur & Katalog',
            'Banner & Spanduk',
            'Meeting dengan Klien',
            'Survey Lokasi',
            'Dokumentasi Pre-wedding',
            'Backup Storage',
            'Keamanan Kantor',
        ];

        // Create 50 expense ops records
        for ($i = 1; $i <= 50; $i++) {
            // Generate dates (all in 2025)
            $expenseDate = Carbon::create(2025, rand(1, 7), rand(1, 28))->addHours(rand(8, 18))->addMinutes(rand(0, 59));

            // Random expense type
            $expenseName = $expenseTypes[array_rand($expenseTypes)];

            // Add variation to expense names
            if (rand(1, 3) === 1) {
                $variations = [
                    ' Bulan '.Carbon::create(2025, rand(1, 7))->format('F'),
                    ' - Kantor Pusat',
                    ' - Cabang',
                    ' Periode '.rand(1, 12).'/2025',
                    ' - Urgent',
                ];
                $expenseName .= $variations[array_rand($variations)];
            }

            // Generate amount based on expense type
            $amount = $this->generateAmount($expenseName);

            // Generate nota dinas number
            $noNd = rand(1000, 9999);

            // Generate realistic notes
            $note = $this->generateNote($expenseName, $amount);

            ExpenseOps::create([
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

        $this->command->info('50 operational expenses created successfully!');
    }

    /**
     * Generate realistic amount based on expense type
     */
    private function generateAmount(string $expenseName): int
    {
        // Different amount ranges based on expense type
        if (str_contains(strtolower($expenseName), 'listrik')) {
            return rand(500000, 2000000); // 500K - 2M for electricity
        } elseif (str_contains(strtolower($expenseName), 'internet') || str_contains(strtolower($expenseName), 'telepon')) {
            return rand(300000, 800000); // 300K - 800K for internet/phone
        } elseif (str_contains(strtolower($expenseName), 'atk') || str_contains(strtolower($expenseName), 'alat tulis')) {
            return rand(100000, 500000); // 100K - 500K for office supplies
        } elseif (str_contains(strtolower($expenseName), 'transportasi') || str_contains(strtolower($expenseName), 'bensin')) {
            return rand(200000, 1000000); // 200K - 1M for transportation
        } elseif (str_contains(strtolower($expenseName), 'konsumsi') || str_contains(strtolower($expenseName), 'meeting')) {
            return rand(150000, 600000); // 150K - 600K for meals/meetings
        } elseif (str_contains(strtolower($expenseName), 'maintenance') || str_contains(strtolower($expenseName), 'service')) {
            return rand(300000, 1500000); // 300K - 1.5M for maintenance
        } elseif (str_contains(strtolower($expenseName), 'software') || str_contains(strtolower($expenseName), 'license')) {
            return rand(500000, 2500000); // 500K - 2.5M for software
        } elseif (str_contains(strtolower($expenseName), 'marketing') || str_contains(strtolower($expenseName), 'banner')) {
            return rand(300000, 1200000); // 300K - 1.2M for marketing
        } elseif (str_contains(strtolower($expenseName), 'asuransi') || str_contains(strtolower($expenseName), 'pajak')) {
            return rand(800000, 3000000); // 800K - 3M for insurance/tax
        } else {
            return rand(100000, 800000); // Default range
        }
    }

    /**
     * Generate realistic notes based on expense type
     */
    private function generateNote(string $expenseName, int $amount): string
    {
        $baseNotes = [
            'Listrik' => 'Pembayaran tagihan listrik kantor untuk periode bulan ini. Konsumsi normal sesuai operasional harian.',
            'Internet' => 'Langganan internet kantor dengan bandwidth dedicated untuk operasional bisnis dan komunikasi dengan klien.',
            'ATK' => 'Pembelian alat tulis kantor untuk keperluan administrasi, dokumentasi, dan operasional harian.',
            'Transportasi' => 'Biaya transportasi untuk survey lokasi wedding, meeting dengan klien, dan koordinasi vendor.',
            'Konsumsi' => 'Konsumsi untuk meeting dengan klien, rapat internal, dan koordinasi tim wedding.',
            'Maintenance' => 'Biaya perawatan dan service peralatan kantor untuk memastikan operasional berjalan lancar.',
            'Software' => 'Langganan software untuk design, editing, dan manajemen project wedding.',
            'Marketing' => 'Biaya promosi dan marketing material untuk pengembangan bisnis wedding organizer.',
            'Bensin' => 'Biaya bahan bakar kendaraan operasional untuk survey, meeting, dan koordinasi acara.',
            'Pulsa' => 'Pulsa dan paket data untuk komunikasi dengan klien dan koordinasi tim di lapangan.',
        ];

        // Find matching note based on expense name
        foreach ($baseNotes as $key => $note) {
            if (str_contains(strtolower($expenseName), strtolower($key))) {
                return $note.' Total pengeluaran: Rp '.number_format($amount, 0, ',', '.');
            }
        }

        // Default note
        return 'Pengeluaran operasional untuk '.strtolower($expenseName).'. Diperlukan untuk mendukung operasional bisnis wedding organizer. Total: Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * Clean previous seeder data to avoid duplicates
     */
    private function cleanPreviousSeederData(): void
    {
        // Find expense ops created by seeder (with specific pattern or all records)
        $seederExpenses = ExpenseOps::withTrashed()->get();

        if ($seederExpenses->count() > 0) {
            $this->command->warn("Found {$seederExpenses->count()} previous expense ops records (including soft deleted).");

            if ($this->command->confirm('Do you want to permanently delete previous ExpenseOps seeder data before creating new ones?', true)) {
                $this->command->info('Permanently cleaning previous ExpenseOpsSeeder data...');

                // Force delete all records
                ExpenseOps::withTrashed()->forceDelete();

                $this->command->info('Previous ExpenseOps seeder data permanently cleaned!');
            } else {
                $this->command->info('Keeping existing data. New expenses will be added.');
            }
        } else {
            $this->command->info('No previous ExpenseOps seeder data found.');
        }
    }
}
