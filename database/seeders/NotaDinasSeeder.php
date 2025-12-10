<?php

namespace Database\Seeders;

use App\Models\NotaDinas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotaDinasSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get required data
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->error('Need at least 2 users. Please run UserSeeder first.');

            return;
        }

        $this->command->info('Creating Nota Dinas records...');

        // Sample Nota Dinas data - Basic headers only
        $notaDinasList = [
            [
                'no_nd' => 'ND/001/VIII/2025',
                'tanggal' => '2025-08-01',
                'sifat' => 'Segera',
                'hal' => 'Permintaan Transfer Vendor Wedding',
                'catatan' => 'Transfer untuk vendor wedding. Mohon segera diproses.',
                'status' => 'disetujui',
            ],
            [
                'no_nd' => 'ND/002/VIII/2025',
                'tanggal' => '2025-08-05',
                'sifat' => 'Biasa',
                'hal' => 'Permintaan Transfer Vendor Catering',
                'catatan' => 'Transfer untuk vendor catering acara wedding.',
                'status' => 'diajukan',
            ],
            [
                'no_nd' => 'ND/003/VIII/2025',
                'tanggal' => '2025-08-10',
                'sifat' => 'Segera',
                'hal' => 'Permintaan Transfer Multiple Vendor',
                'catatan' => 'Transfer untuk beberapa vendor acara wedding.',
                'status' => 'draft',
            ],
            [
                'no_nd' => 'ND/004/VIII/2025',
                'tanggal' => '2025-08-12',
                'sifat' => 'Urgent',
                'hal' => 'Transfer Pelunasan Vendor Musik',
                'catatan' => 'Pelunasan untuk vendor musik dan sound system.',
                'status' => 'disetujui',
            ],
            [
                'no_nd' => 'ND/005/VIII/2025',
                'tanggal' => '2025-08-15',
                'sifat' => 'Biasa',
                'hal' => 'Transfer Vendor Makeup & Busana',
                'catatan' => 'Transfer untuk vendor makeup artist dan sewa busana pengantin.',
                'status' => 'diajukan',
            ],
            [
                'no_nd' => 'ND/006/VIII/2025',
                'tanggal' => '2025-08-18',
                'sifat' => 'Segera',
                'hal' => 'Transfer Vendor Operasional Kantor',
                'catatan' => 'Transfer untuk keperluan operasional kantor bulan ini.',
                'status' => 'disetujui',
            ],
            [
                'no_nd' => 'ND/007/VIII/2025',
                'tanggal' => '2025-08-20',
                'sifat' => 'Biasa',
                'hal' => 'Transfer Vendor Maintenance Equipment',
                'catatan' => 'Transfer untuk maintenance peralatan kantor dan studio.',
                'status' => 'draft',
            ],
            [
                'no_nd' => 'ND/008/VIII/2025',
                'tanggal' => '2025-08-25',
                'sifat' => 'Urgent',
                'hal' => 'Transfer Emergency Vendor',
                'catatan' => 'Transfer emergency untuk vendor pengganti mendadak.',
                'status' => 'diajukan',
            ],
            [
                'no_nd' => 'ND/009/IX/2025',
                'tanggal' => '2025-09-01',
                'sifat' => 'Segera',
                'hal' => 'Transfer Vendor Wedding September',
                'catatan' => 'Transfer untuk vendor wedding di bulan September.',
                'status' => 'disetujui',
            ],
            [
                'no_nd' => 'ND/010/IX/2025',
                'tanggal' => '2025-09-05',
                'sifat' => 'Biasa',
                'hal' => 'Transfer Vendor Operasional September',
                'catatan' => 'Transfer untuk keperluan operasional bulan September.',
                'status' => 'draft',
            ],
        ];

        $created = 0;

        foreach ($notaDinasList as $ndData) {
            $notaDinas = NotaDinas::firstOrCreate(
                ['no_nd' => $ndData['no_nd']],
                [
                    'tanggal' => $ndData['tanggal'],
                    'pengirim_id' => $users->random()->id,
                    'penerima_id' => $users->random()->id,
                    'sifat' => $ndData['sifat'],
                    'hal' => $ndData['hal'],
                    'catatan' => $ndData['catatan'],
                    'status' => $ndData['status'],
                    'approved_by' => $ndData['status'] === 'disetujui' ? $users->random()->id : null,
                    'approved_at' => $ndData['status'] === 'disetujui' ? Carbon::now()->subDays(rand(1, 30)) : null,
                ]
            );

            $created++;
            $this->command->info("✅ Created Nota Dinas: {$ndData['no_nd']}");
        }

        $this->command->info('🎉 NotaDinas seeder completed successfully!');
        $this->command->info("📊 Created {$created} Nota Dinas header records");

        // Show summary
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Total Nota Dinas', $created],
                ['Draft Status', NotaDinas::where('status', 'draft')->count()],
                ['Diajukan Status', NotaDinas::where('status', 'diajukan')->count()],
                ['Disetujui Status', NotaDinas::where('status', 'disetujui')->count()],
            ]
        );

        $this->command->info('💡 Note: Use NotaDinasDetailSeeder to add detail records to these Nota Dinas.');
    }
}
