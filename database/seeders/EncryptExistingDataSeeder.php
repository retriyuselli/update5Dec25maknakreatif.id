<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EncryptExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Mulai enkripsi data sensitif yang sudah ada...');

        $totalEncrypted = 0;
        $errors = 0;

        // Get all data pribadi records dengan data plaintext
        $records = DB::table('data_pribadis')
            ->whereNotNull('gaji')
            ->orWhereNotNull('nomor_telepon')
            ->orWhereNotNull('alamat')
            ->get();

        $this->command->info("📊 Ditemukan {$records->count()} record untuk dienkripsi");

        foreach ($records as $record) {
            try {
                $updateData = [];

                // Encrypt gaji if exists and not already encrypted
                if (! empty($record->gaji) && empty($record->gaji_encrypted)) {
                    $updateData['gaji_encrypted'] = Crypt::encryptString((string) $record->gaji);
                    $this->command->info("💰 Mengenkripsi gaji untuk ID: {$record->id}");
                }

                // Encrypt nomor_telepon if exists and not already encrypted
                if (! empty($record->nomor_telepon) && empty($record->nomor_telepon_encrypted)) {
                    $cleanedPhone = preg_replace('/^(\+62|0)/', '', $record->nomor_telepon);
                    $updateData['nomor_telepon_encrypted'] = Crypt::encryptString($cleanedPhone);
                    $this->command->info("📱 Mengenkripsi nomor telepon untuk ID: {$record->id}");
                }

                // Encrypt alamat if exists and not already encrypted
                if (! empty($record->alamat) && empty($record->alamat_encrypted)) {
                    $updateData['alamat_encrypted'] = Crypt::encryptString($record->alamat);
                    $this->command->info("🏠 Mengenkripsi alamat untuk ID: {$record->id}");
                }

                // Update record if there's data to encrypt
                if (! empty($updateData)) {
                    DB::table('data_pribadis')
                        ->where('id', $record->id)
                        ->update($updateData);

                    $totalEncrypted++;

                    // Log audit trail
                    Log::info('Personal data encrypted during migration', [
                        'data_pribadi_id' => $record->id,
                        'fields_encrypted' => array_keys($updateData),
                        'migration_process' => true,
                    ]);
                }

            } catch (\Exception $e) {
                $errors++;
                $this->command->error("❌ Error encrypting data for ID {$record->id}: ".$e->getMessage());

                Log::error('Failed to encrypt personal data during migration', [
                    'data_pribadi_id' => $record->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->command->info("\n🎉 ENKRIPSI SELESAI!");
        $this->command->info("✅ Total record berhasil dienkripsi: {$totalEncrypted}");

        if ($errors > 0) {
            $this->command->warn("⚠️  Total error: {$errors}");
        }

        // Clear plaintext data setelah enkripsi berhasil
        if ($totalEncrypted > 0 && $errors === 0) {
            $this->clearPlaintextData();
        }
    }

    /**
     * Clear plaintext sensitive data after successful encryption
     */
    private function clearPlaintextData(): void
    {
        $this->command->info("\n🧹 Membersihkan data plaintext...");

        try {
            DB::table('data_pribadis')
                ->whereNotNull('gaji_encrypted')
                ->update(['gaji' => null]);

            DB::table('data_pribadis')
                ->whereNotNull('nomor_telepon_encrypted')
                ->update(['nomor_telepon' => null]);

            DB::table('data_pribadis')
                ->whereNotNull('alamat_encrypted')
                ->update(['alamat' => null]);

            $this->command->info('✅ Data plaintext berhasil dibersihkan');

            Log::info('Plaintext sensitive data cleared after encryption', [
                'process' => 'data_security_migration',
                'action' => 'clear_plaintext',
            ]);

        } catch (\Exception $e) {
            $this->command->error('❌ Error clearing plaintext data: '.$e->getMessage());

            Log::error('Failed to clear plaintext data after encryption', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
