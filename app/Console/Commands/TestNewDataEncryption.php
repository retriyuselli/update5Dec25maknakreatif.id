<?php

namespace App\Console\Commands;

use App\Models\DataPribadi;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestNewDataEncryption extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:test-new-data';

    /**
     * The console command description.
     */
    protected $description = 'Test automatic encryption for new data entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing automatic encryption untuk data baru...');

        // Create test data
        $testData = [
            'nama_lengkap' => 'Test User Encryption',
            'email' => 'test.encryption@makna.com',
            'nomor_telepon' => '081234567890',
            'gaji' => 5000000,
            'alamat' => 'Jl. Test Encryption No. 123, Jakarta',
        ];

        $this->info('📝 Membuat data test baru...');

        try {
            // Create new record
            $newRecord = DataPribadi::create($testData);
            $this->info("✅ Data berhasil dibuat dengan ID: {$newRecord->id}");

            // Check database directly untuk verify encryption
            $dbRecord = DB::table('data_pribadis')->where('id', $newRecord->id)->first();

            $this->line('');
            $this->info('🔍 Verifikasi enkripsi di database:');

            // Check if sensitive data is encrypted
            $gajiEncrypted = ! empty($dbRecord->gaji_encrypted);
            $phoneEncrypted = ! empty($dbRecord->nomor_telepon_encrypted);
            $addressEncrypted = ! empty($dbRecord->alamat_encrypted);

            // Check if plaintext is cleared
            $gajiPlaintext = ! empty($dbRecord->gaji);
            $phonePlaintext = ! empty($dbRecord->nomor_telepon);
            $addressPlaintext = ! empty($dbRecord->alamat);

            $this->line('💰 GAJI:');
            $gajiEncrypted ?
                $this->info('   ✅ Encrypted: Ya') :
                $this->error('   ❌ Encrypted: Tidak');
            $gajiPlaintext ?
                $this->error('   ❌ Plaintext: Ada') :
                $this->info('   ✅ Plaintext: Kosong');

            $this->line('📱 NOMOR TELEPON:');
            $phoneEncrypted ?
                $this->info('   ✅ Encrypted: Ya') :
                $this->error('   ❌ Encrypted: Tidak');
            $phonePlaintext ?
                $this->error('   ❌ Plaintext: Ada') :
                $this->info('   ✅ Plaintext: Kosong');

            $this->line('🏠 ALAMAT:');
            $addressEncrypted ?
                $this->info('   ✅ Encrypted: Ya') :
                $this->error('   ❌ Encrypted: Tidak');
            $addressPlaintext ?
                $this->error('   ❌ Plaintext: Ada') :
                $this->info('   ✅ Plaintext: Kosong');

            // Test decryption via model
            $this->line('');
            $this->info('🔓 Testing dekripsi via model:');

            $decryptedSalary = $newRecord->gaji;
            $decryptedPhone = $newRecord->nomor_telepon;
            $decryptedAddress = $newRecord->alamat;

            $this->info('   💰 Gaji: Rp '.number_format($decryptedSalary, 0, ',', '.'));
            $this->info("   📱 Telepon: {$decryptedPhone}");
            $this->info("   🏠 Alamat: {$decryptedAddress}");

            // Verify values match
            $salaryMatch = $decryptedSalary == $testData['gaji'];
            $phoneMatch = $decryptedPhone == preg_replace('/^(\+62|0)/', '', $testData['nomor_telepon']);
            $addressMatch = $decryptedAddress == $testData['alamat'];

            $this->line('');
            $this->info('🎯 Verifikasi data integrity:');
            $salaryMatch ?
                $this->info('   ✅ Gaji: Match') :
                $this->error('   ❌ Gaji: Tidak match');
            $phoneMatch ?
                $this->info('   ✅ Telepon: Match') :
                $this->error('   ❌ Telepon: Tidak match');
            $addressMatch ?
                $this->info('   ✅ Alamat: Match') :
                $this->error('   ❌ Alamat: Tidak match');

            // Overall result
            $allEncrypted = $gajiEncrypted && $phoneEncrypted && $addressEncrypted;
            $noPlaintext = ! $gajiPlaintext && ! $phonePlaintext && ! $addressPlaintext;
            $allMatch = $salaryMatch && $phoneMatch && $addressMatch;

            $this->line('');
            if ($allEncrypted && $noPlaintext && $allMatch) {
                $this->info('🎉 HASIL: DATA BARU OTOMATIS AMAN ✅');
                $this->info('   Enkripsi berfungsi sempurna untuk data baru!');
            } else {
                $this->error('❌ HASIL: ADA MASALAH DENGAN ENKRIPSI OTOMATIS');
            }

            // Cleanup test data
            $this->line('');
            $this->info('🧹 Membersihkan data test...');
            $newRecord->delete();
            $this->info('✅ Data test berhasil dihapus');

        } catch (Exception $e) {
            $this->error('❌ Error: '.$e->getMessage());
            $this->error('Trace: '.$e->getTraceAsString());
        }

        return Command::SUCCESS;
    }
}
