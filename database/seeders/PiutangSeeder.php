<?php

namespace Database\Seeders;

use App\Enums\JenisPiutang;
use App\Enums\StatusPiutang;
use App\Models\PaymentMethod;
use App\Models\Piutang;
use Illuminate\Database\Seeder;

class PiutangSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Piutang...');

        $methods = PaymentMethod::all();
        if ($methods->isEmpty()) {
            $this->command->error('No PaymentMethod found. Please run PaymentMethodSeeder first.');
            return;
        }

        $items = [
            [
                'nomor_piutang' => 'AR-2025-001',
                'jenis_piutang' => JenisPiutang::BISNIS,
                'nama_debitur' => 'CV Citra Wedding',
                'kontak_debitur' => '+62-812-0000-1111',
                'keterangan' => 'Pelunasan paket wedding premium',
                'jumlah_pokok' => 15000000,
                'persentase_bunga' => 0,
                'total_piutang' => 15000000,
                'sudah_dibayar' => 5000000,
                'sisa_piutang' => 10000000,
                'tanggal_piutang' => '2025-01-10',
                'tanggal_jatuh_tempo' => '2025-02-10',
                'status' => StatusPiutang::DIBAYAR_SEBAGIAN,
                'prioritas' => 'sedang',
            ],
            [
                'nomor_piutang' => 'AR-2025-002',
                'jenis_piutang' => JenisPiutang::OPERASIONAL,
                'nama_debitur' => 'PT Media Kreatif Nusantara',
                'kontak_debitur' => '+62-813-2222-3333',
                'keterangan' => 'Jasa workshop fotografi',
                'jumlah_pokok' => 8000000,
                'persentase_bunga' => 0,
                'total_piutang' => 8000000,
                'sudah_dibayar' => 0,
                'sisa_piutang' => 8000000,
                'tanggal_piutang' => '2025-01-15',
                'tanggal_jatuh_tempo' => '2025-02-15',
                'status' => StatusPiutang::AKTIF,
                'prioritas' => 'tinggi',
            ],
        ];

        $userId = \App\Models\User::query()->inRandomOrder()->value('id');
        foreach ($items as $data) {
            Piutang::firstOrCreate(
                ['nomor_piutang' => $data['nomor_piutang']],
                array_merge($data, ['dibuat_oleh' => $userId])
            );
        }

        $this->command->info('Piutang seeded.');
    }
}
