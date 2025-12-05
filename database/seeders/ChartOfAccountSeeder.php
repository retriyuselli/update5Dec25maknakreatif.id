<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Berdasarkan struktur COA yang ada di gambar
     *
     * IMPORTANT: Sistem journal otomatis membutuhkan akun dengan kode 4-digit:
     * - 1100 (Kas) - untuk transaksi kas
     * - 1200 (Bank) - untuk transaksi bank
     * - 1300 (Piutang Usaha) - untuk piutang
     * - 4100 (Pendapatan Jasa Wedding) - untuk pendapatan order
     * - 5100 (Biaya Proyek Wedding) - untuk expense journals
     */
    public function run(): void
    {
        $this->command->info('Creating Chart of Accounts...');

        // Cek apakah sudah ada data
        if (ChartOfAccount::count() > 0) {
            $this->command->info('Chart of Accounts already exists. Checking journal accounts...');
            $this->checkJournalAccounts();

            return;
        }

        // 1. HARTA (ASSETS)
        $harta = ChartOfAccount::create([
            'account_code' => '1000',
            'account_name' => 'Harta',
            'account_type' => 'HARTA',
            'level' => 1,
            'normal_balance' => 'debit',
            'description' => 'Aset atau harta perusahaan',
        ]);

        // Sub accounts untuk Harta - Akun detail untuk sistem journal
        ChartOfAccount::create([
            'account_code' => '1100',
            'account_name' => 'Kas',
            'account_type' => 'HARTA',
            'parent_id' => $harta->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Akun kas untuk sistem journal otomatis',
        ]);

        ChartOfAccount::create([
            'account_code' => '1200',
            'account_name' => 'Bank',
            'account_type' => 'HARTA',
            'parent_id' => $harta->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Akun bank untuk sistem journal otomatis',
        ]);

        ChartOfAccount::create([
            'account_code' => '1300',
            'account_name' => 'Piutang Usaha',
            'account_type' => 'HARTA',
            'parent_id' => $harta->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Akun piutang untuk sistem journal otomatis',
        ]);

        // 2. KEWAJIBAN (LIABILITIES)
        $kewajiban = ChartOfAccount::create([
            'account_code' => '2000',
            'account_name' => 'Kewajiban',
            'account_type' => 'KEWAJIBAN',
            'level' => 1,
            'normal_balance' => 'credit',
            'description' => 'Kewajiban atau utang perusahaan',
        ]);

        // 3. MODAL (EQUITY)
        $modal = ChartOfAccount::create([
            'account_code' => '3000',
            'account_name' => 'Modal',
            'account_type' => 'MODAL',
            'level' => 1,
            'normal_balance' => 'credit',
            'description' => 'Modal atau ekuitas perusahaan',
        ]);

        // 4. PENDAPATAN (REVENUE)
        $pendapatan = ChartOfAccount::create([
            'account_code' => '4000',
            'account_name' => 'Pendapatan',
            'account_type' => 'PENDAPATAN',
            'level' => 1,
            'normal_balance' => 'credit',
            'description' => 'Pendapatan dari operasional perusahaan',
        ]);

        // Sub account untuk Pendapatan - Akun detail untuk sistem journal
        ChartOfAccount::create([
            'account_code' => '4100',
            'account_name' => 'Pendapatan Jasa Wedding',
            'account_type' => 'PENDAPATAN',
            'parent_id' => $pendapatan->id,
            'level' => 3,
            'normal_balance' => 'credit',
            'description' => 'Akun pendapatan jasa wedding untuk sistem journal otomatis',
        ]);

        // 5. BEBAN ATAS PENDAPATAN (COGS)
        $bebanAtasPendapatan = ChartOfAccount::create([
            'account_code' => '5000',
            'account_name' => 'Beban Atas Pendapatan',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'level' => 1,
            'normal_balance' => 'debit',
            'description' => 'Harga Pokok Penjualan dan beban langsung',
        ]);

        // Sub accounts untuk Beban Atas Pendapatan (sesuai gambar)
        ChartOfAccount::create([
            'account_code' => '5200',
            'account_name' => 'Beban Pembelian',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'parent_id' => $bebanAtasPendapatan->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Biaya pembelian untuk operasional',
        ]);

        ChartOfAccount::create([
            'account_code' => '5300',
            'account_name' => 'Harga Pokok Penjualan Umum',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'parent_id' => $bebanAtasPendapatan->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'HPP untuk penjualan umum',
        ]);

        ChartOfAccount::create([
            'account_code' => '5400',
            'account_name' => 'Retur Pembelian',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'parent_id' => $bebanAtasPendapatan->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Pengembalian barang pembelian',
        ]);

        ChartOfAccount::create([
            'account_code' => '5500',
            'account_name' => 'Diskon Pembelian',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'parent_id' => $bebanAtasPendapatan->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Diskon yang diberikan pada pembelian',
        ]);

        ChartOfAccount::create([
            'account_code' => '5600',
            'account_name' => 'Beban Atas Pendapatan Lain',
            'account_type' => 'BEBAN_ATAS_PENDAPATAN',
            'parent_id' => $bebanAtasPendapatan->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Beban yang terkait dengan pendapatan lain',
        ]);

        // 6. BEBAN OPERASIONAL (OPERATING EXPENSES)
        $bebanOperasional = ChartOfAccount::create([
            'account_code' => '6000',
            'account_name' => 'Beban Operasional',
            'account_type' => 'BEBAN_OPERASIONAL',
            'level' => 1,
            'normal_balance' => 'debit',
            'description' => 'Beban operasional perusahaan',
        ]);

        // Sub account untuk Beban Operasional - Akun detail untuk sistem journal
        ChartOfAccount::create([
            'account_code' => '5100',
            'account_name' => 'Biaya Proyek Wedding',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanOperasional->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Akun biaya proyek wedding untuk sistem journal otomatis',
        ]);

        // Sub accounts untuk Beban Operasional (sesuai gambar)
        $bebanPemasaran = ChartOfAccount::create([
            'account_code' => '6100',
            'account_name' => 'Beban Pemasaran Dan Penjualan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanOperasional->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Biaya untuk pemasaran dan penjualan',
        ]);

        // Detail Beban Pemasaran Dan Penjualan
        ChartOfAccount::create([
            'account_code' => '6101',
            'account_name' => 'Beban Iklan dan Promosi',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanPemasaran->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya iklan, promosi, dan marketing',
        ]);

        ChartOfAccount::create([
            'account_code' => '6102',
            'account_name' => 'Beban Komisi Penjualan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanPemasaran->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Komisi untuk sales dan marketing',
        ]);

        ChartOfAccount::create([
            'account_code' => '6103',
            'account_name' => 'Beban Transportasi Penjualan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanPemasaran->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya transportasi untuk kegiatan penjualan',
        ]);

        ChartOfAccount::create([
            'account_code' => '6104',
            'account_name' => 'Beban Entertainment',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanPemasaran->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya hiburan untuk client dan prospect',
        ]);

        $bebanAdministrasi = ChartOfAccount::create([
            'account_code' => '6200',
            'account_name' => 'Beban Administrasi Dan Umum',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanOperasional->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Biaya administrasi dan umum',
        ]);

        // Detail Beban Administrasi Dan Umum
        ChartOfAccount::create([
            'account_code' => '6201',
            'account_name' => 'Beban Gaji Karyawan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Gaji karyawan dan staff',
        ]);

        ChartOfAccount::create([
            'account_code' => '6202',
            'account_name' => 'Beban Tunjangan Karyawan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Tunjangan, bonus, dan benefits karyawan',
        ]);

        ChartOfAccount::create([
            'account_code' => '6203',
            'account_name' => 'Beban Sewa Kantor',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Sewa kantor dan ruang usaha',
        ]);

        ChartOfAccount::create([
            'account_code' => '6204',
            'account_name' => 'Beban Listrik dan Air',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya utilitas listrik dan air',
        ]);

        ChartOfAccount::create([
            'account_code' => '6205',
            'account_name' => 'Beban Telepon dan Internet',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya telekomunikasi dan internet',
        ]);

        ChartOfAccount::create([
            'account_code' => '6206',
            'account_name' => 'Beban Supplies Kantor',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Alat tulis dan perlengkapan kantor',
        ]);

        ChartOfAccount::create([
            'account_code' => '6207',
            'account_name' => 'Beban Reparasi dan Pemeliharaan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya perbaikan dan maintenance',
        ]);

        ChartOfAccount::create([
            'account_code' => '6208',
            'account_name' => 'Beban Penyusutan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Penyusutan aset tetap',
        ]);

        ChartOfAccount::create([
            'account_code' => '6209',
            'account_name' => 'Beban Asuransi',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Premi asuransi perusahaan',
        ]);

        ChartOfAccount::create([
            'account_code' => '6210',
            'account_name' => 'Beban Pajak dan Retribusi',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Pajak daerah dan retribusi',
        ]);

        ChartOfAccount::create([
            'account_code' => '6211',
            'account_name' => 'Beban Bank dan Administrasi',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya administrasi bank dan transfer',
        ]);

        ChartOfAccount::create([
            'account_code' => '6212',
            'account_name' => 'Beban Training dan Pengembangan',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanAdministrasi->id,
            'level' => 3,
            'normal_balance' => 'debit',
            'description' => 'Biaya pelatihan dan pengembangan SDM',
        ]);

        ChartOfAccount::create([
            'account_code' => '6900',
            'account_name' => 'Beban Operasional Lain',
            'account_type' => 'BEBAN_OPERASIONAL',
            'parent_id' => $bebanOperasional->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Beban operasional lainnya',
        ]);

        // 8. PENDAPATAN LAIN (OTHER INCOME)
        $pendapatanLain = ChartOfAccount::create([
            'account_code' => '8000',
            'account_name' => 'Pendapatan Lain',
            'account_type' => 'PENDAPATAN_LAIN',
            'level' => 1,
            'normal_balance' => 'credit',
            'description' => 'Pendapatan di luar operasional utama',
        ]);

        // 9. BEBAN LAIN (OTHER EXPENSES)
        $bebanLain = ChartOfAccount::create([
            'account_code' => '9000',
            'account_name' => 'Beban Lain',
            'account_type' => 'BEBAN_LAIN',
            'level' => 1,
            'normal_balance' => 'debit',
            'description' => 'Beban di luar operasional utama',
        ]);

        // Sub accounts untuk Beban Lain (sesuai gambar)
        ChartOfAccount::create([
            'account_code' => '9100',
            'account_name' => 'Beban Luar Usaha',
            'account_type' => 'BEBAN_LAIN',
            'parent_id' => $bebanLain->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Beban di luar kegiatan usaha utama',
        ]);

        ChartOfAccount::create([
            'account_code' => '9900',
            'account_name' => 'Beban Pajak',
            'account_type' => 'BEBAN_LAIN',
            'parent_id' => $bebanLain->id,
            'level' => 2,
            'normal_balance' => 'debit',
            'description' => 'Beban pajak perusahaan',
        ]);

        $this->command->info('Chart of Accounts created successfully!');
        $this->command->info('Total accounts created: '.ChartOfAccount::count());

        // Verifikasi akun-akun yang diperlukan sistem journal
        $journalAccounts = ['1100', '1200', '1300', '4100', '5100'];
        $this->command->info('');
        $this->command->info('🏦 Journal System Required Accounts:');
        foreach ($journalAccounts as $code) {
            $account = ChartOfAccount::where('account_code', $code)->first();
            if ($account) {
                $this->command->info("✅ {$code} - {$account->account_name}");
            } else {
                $this->command->error("❌ {$code} - MISSING!");
            }
        }
        $this->command->info('📝 Chart of Accounts ready for automatic journal generation!');
    }

    /**
     * Check if required journal accounts exist
     */
    private function checkJournalAccounts(): void
    {
        // Verifikasi akun-akun yang diperlukan sistem journal
        $journalAccounts = ['1100', '1200', '1300', '4100', '5100'];
        $this->command->info('');
        $this->command->info('🏦 Journal System Required Accounts:');

        $missingAccounts = [];
        foreach ($journalAccounts as $code) {
            $account = ChartOfAccount::where('account_code', $code)->first();
            if ($account) {
                $this->command->info("✅ {$code} - {$account->account_name}");
            } else {
                $this->command->error("❌ {$code} - MISSING!");
                $missingAccounts[] = $code;
            }
        }

        if (! empty($missingAccounts)) {
            $this->command->warn('🔧 Creating missing journal accounts...');
            $this->createMissingJournalAccounts($missingAccounts);
        } else {
            $this->command->info('✅ All journal accounts are ready!');
        }
    }

    /**
     * Create missing journal accounts
     */
    private function createMissingJournalAccounts(array $missingCodes): void
    {
        $accountDefinitions = [
            '1100' => ['name' => 'Kas', 'type' => 'HARTA', 'parent_code' => '1000'],
            '1200' => ['name' => 'Bank', 'type' => 'HARTA', 'parent_code' => '1000'],
            '1300' => ['name' => 'Piutang Usaha', 'type' => 'HARTA', 'parent_code' => '1000'],
            '4100' => ['name' => 'Pendapatan Jasa Wedding', 'type' => 'PENDAPATAN', 'parent_code' => '4000'],
            '5100' => ['name' => 'Biaya Proyek Wedding', 'type' => 'BEBAN_OPERASIONAL', 'parent_code' => '6000'],
        ];

        foreach ($missingCodes as $code) {
            if (isset($accountDefinitions[$code])) {
                $def = $accountDefinitions[$code];
                $parent = ChartOfAccount::where('account_code', $def['parent_code'])->first();

                ChartOfAccount::create([
                    'account_code' => $code,
                    'account_name' => $def['name'],
                    'account_type' => $def['type'],
                    'parent_id' => $parent ? $parent->id : null,
                    'level' => 3,
                    'normal_balance' => in_array($code[0], ['1', '5']) ? 'debit' : 'credit',
                    'description' => 'Akun untuk sistem journal otomatis',
                ]);

                $this->command->info("✅ Created: {$code} - {$def['name']}");
            }
        }
    }
}
