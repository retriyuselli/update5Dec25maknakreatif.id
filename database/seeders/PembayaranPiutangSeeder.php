<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\PembayaranPiutang;
use App\Models\Piutang;
use Illuminate\Database\Seeder;

class PembayaranPiutangSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Pembayaran Piutang...');

        $methods = PaymentMethod::all();
        $piutangs = Piutang::all();

        if ($methods->isEmpty() || $piutangs->isEmpty()) {
            $this->command->error('Need PaymentMethod and Piutang. Run PaymentMethodSeeder & PiutangSeeder first.');
            return;
        }

        $payments = [
            [
                'nomor_pembayaran' => 'ARPAY-2025-001',
                'jumlah_pembayaran' => 5000000,
                'jumlah_bunga' => 0,
                'denda' => 0,
                'total_pembayaran' => 5000000,
                'tanggal_pembayaran' => '2025-01-25',
                'tanggal_dicatat' => '2025-01-25',
                'nomor_referensi' => 'BANK-REF-001',
                'bukti_pembayaran' => [],
                'catatan' => 'Pembayaran sebagian',
                'status' => 'dikonfirmasi',
            ],
        ];

        $piutang = $piutangs->first();
        $method = $methods->first();

        $payerId = \App\Models\User::query()->inRandomOrder()->value('id');
        $confirmerId = \App\Models\User::query()->inRandomOrder()->value('id');

        foreach ($payments as $data) {
            PembayaranPiutang::firstOrCreate(
                ['nomor_pembayaran' => $data['nomor_pembayaran']],
                array_merge($data, [
                    'piutang_id' => $piutang->id,
                    'payment_method_id' => $method->id,
                    'dibayar_oleh' => $payerId,
                    'dikonfirmasi_oleh' => $confirmerId,
                ])
            );
        }

        $this->command->info('Pembayaran Piutang seeded.');
    }
}
