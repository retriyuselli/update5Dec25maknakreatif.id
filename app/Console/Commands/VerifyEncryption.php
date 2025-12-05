<?php

namespace App\Console\Commands;

use App\Models\DataPribadi;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyEncryption extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:verify-encryption';

    /**
     * The console command description.
     */
    protected $description = 'Verify that sensitive data is properly encrypted';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Memverifikasi status enkripsi data sensitif...');

        // Check total records
        $totalRecords = DataPribadi::count();
        $this->info("📊 Total data pribadi: {$totalRecords}");

        // Check encrypted salary data
        $encryptedSalary = DB::table('data_pribadis')
            ->whereNotNull('gaji_encrypted')
            ->count();

        // Check remaining plaintext salary
        $plaintextSalary = DB::table('data_pribadis')
            ->whereNotNull('gaji')
            ->count();

        // Check encrypted phone numbers
        $encryptedPhone = DB::table('data_pribadis')
            ->whereNotNull('nomor_telepon_encrypted')
            ->count();

        // Check remaining plaintext phone
        $plaintextPhone = DB::table('data_pribadis')
            ->whereNotNull('nomor_telepon')
            ->count();

        // Check encrypted addresses
        $encryptedAddress = DB::table('data_pribadis')
            ->whereNotNull('alamat_encrypted')
            ->count();

        // Check remaining plaintext address
        $plaintextAddress = DB::table('data_pribadis')
            ->whereNotNull('alamat')
            ->count();

        $this->line('');
        $this->info('💰 STATUS ENKRIPSI GAJI:');
        $this->line("   ✅ Encrypted: {$encryptedSalary}");
        $plaintextSalary > 0 ?
            $this->error("   ❌ Plaintext: {$plaintextSalary}") :
            $this->info("   ✅ Plaintext: {$plaintextSalary}");

        $this->line('');
        $this->info('📱 STATUS ENKRIPSI NOMOR TELEPON:');
        $this->line("   ✅ Encrypted: {$encryptedPhone}");
        $plaintextPhone > 0 ?
            $this->error("   ❌ Plaintext: {$plaintextPhone}") :
            $this->info("   ✅ Plaintext: {$plaintextPhone}");

        $this->line('');
        $this->info('🏠 STATUS ENKRIPSI ALAMAT:');
        $this->line("   ✅ Encrypted: {$encryptedAddress}");
        $plaintextAddress > 0 ?
            $this->error("   ❌ Plaintext: {$plaintextAddress}") :
            $this->info("   ✅ Plaintext: {$plaintextAddress}");

        // Test decrypt functionality
        $this->line('');
        $this->info('🧪 Testing dekripsi...');

        $testRecord = DataPribadi::whereNotNull('gaji_encrypted')->first();
        if ($testRecord) {
            try {
                $decryptedSalary = $testRecord->gaji;
                $this->info('   ✅ Dekripsi gaji berhasil: Rp '.number_format($decryptedSalary, 0, ',', '.'));

                $decryptedPhone = $testRecord->nomor_telepon;
                $this->info("   ✅ Dekripsi nomor telepon berhasil: {$decryptedPhone}");

                $decryptedAddress = $testRecord->alamat;
                $this->info('   ✅ Dekripsi alamat berhasil: '.substr($decryptedAddress, 0, 30).'...');

            } catch (Exception $e) {
                $this->error('   ❌ Error dekripsi: '.$e->getMessage());
            }
        }

        // Overall security status
        $this->line('');
        $securityLevel = ($plaintextSalary + $plaintextPhone + $plaintextAddress) === 0 ? 'AMAN' : 'PERLU PERBAIKAN';
        $securityLevel === 'AMAN' ?
            $this->info("🛡️  STATUS KEAMANAN: {$securityLevel}") :
            $this->error("🛡️  STATUS KEAMANAN: {$securityLevel}");

        return Command::SUCCESS;
    }
}
