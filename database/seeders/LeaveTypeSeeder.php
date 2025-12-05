<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏖️  Creating Leave Types with detailed descriptions...');
        $this->command->newLine();

        // Data jenis cuti dengan keterangan lengkap sesuai peraturan
        $leaveTypes = [
            [
                'name' => 'Cuti Tahunan',
                'max_days_per_year' => 12,
                'keterangan' => 'Hak minimal 12 hari kerja setelah 1 tahun masa kerja (sesuai UU Ketenagakerjaan).',
            ],
            [
                'name' => 'Cuti Sakit',
                'max_days_per_year' => 3,
                'keterangan' => 'Cuti dikarenakan sakit dengan menunjukkan surat dokter, jika lewat dari 2 hari akan memotong cuti tahunan.',
            ],
            [
                'name' => 'Cuti Ibadah',
                'max_days_per_year' => 3,
                'keterangan' => 'Cuti hari raya besar (Idul Fitri, Idul Adha).',
            ],
            [
                'name' => 'Cuti Menikah',
                'max_days_per_year' => 3,
                'keterangan' => 'Cuti dikarenakan menikah.',
            ],
            [
                'name' => 'Cuti Melahirkan',
                'max_days_per_year' => 90,
                'keterangan' => 'Sesuai UU: 1,5 bulan sebelum dan 1,5 bulan setelah melahirkan.',
            ],
            [
                'name' => 'Cuti Keluarga',
                'max_days_per_year' => 3,
                'keterangan' => 'Cuti dikarenakan keluarga segaris sakit keras/meninggal.',
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);

            // Display detailed info for each leave type
            $this->command->line("✅ {$leaveType['name']} - {$leaveType['max_days_per_year']} hari per tahun");
            $this->command->line("   → {$leaveType['keterangan']}");
            $this->command->newLine();
        }

        $this->command->info('✅ LeaveType seeder completed successfully!');
        $this->command->info('📊 Total created: '.count($leaveTypes).' leave types');
    }
}
