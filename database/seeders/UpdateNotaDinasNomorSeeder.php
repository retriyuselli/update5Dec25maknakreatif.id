<?php

namespace Database\Seeders;

use App\Models\NotaDinas;
use Illuminate\Database\Seeder;

class UpdateNotaDinasNomorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder ini mengupdate nomor nota dinas yang sudah ada
     * dengan format lama menjadi format baru dengan kategori
     */
    public function run(): void
    {
        // Get all existing nota dinas
        $notaDinas = NotaDinas::withTrashed()->get();

        foreach ($notaDinas as $nd) {
            $oldNumber = $nd->no_nd;
            $kategori = 'BIS'; // default kategori
            $tahun = $nd->tanggal ? $nd->tanggal->format('Y') : date('Y');

            // Deteksi kategori berdasarkan nomor lama
            if (str_contains(strtolower($oldNumber), 'bis')) {
                $kategori = 'BIS';
            } elseif (str_contains(strtolower($oldNumber), 'ops')) {
                $kategori = 'OPS';
            } elseif (str_contains(strtolower($oldNumber), 'adm')) {
                $kategori = 'ADM';
            }

            // Extract angka dari nomor lama
            preg_match('/(\d+)/', $oldNumber, $matches);
            $nomor = isset($matches[1]) ? $matches[1] : '1';
            $formattedNumber = str_pad($nomor, 3, '0', STR_PAD_LEFT);

            // Generate nomor baru dengan format: ND/KATEGORI/NOMOR/TAHUN
            $newNumber = "ND/{$kategori}/{$formattedNumber}/{$tahun}";

            // Update record
            $nd->update([
                'kategori_nd' => $kategori,
                'no_nd' => $newNumber,
            ]);

            $this->command->info("Updated: {$oldNumber} -> {$newNumber}");
        }

        $this->command->info('Seeder completed. Updated '.$notaDinas->count().' records.');
    }
}
