<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Sarah Wijaya',
                'bank_name' => 'Central Asia (BCA)',
                'cabang' => 'Sudirman',
                'no_rekening' => '1234567890',
                'is_cash' => false,
                'opening_balance' => 50000000, // Rp 50 juta
                'opening_balance_date' => Carbon::parse('2024-01-01'),
            ],
            [
                'name' => 'Michael Chen',
                'bank_name' => 'Mandiri',
                'cabang' => 'Gatot Subroto',
                'no_rekening' => '9876543210',
                'is_cash' => false,
                'opening_balance' => 25000000, // Rp 25 juta
                'opening_balance_date' => Carbon::parse('2024-01-01'),
            ],
            [
                'name' => 'Makna Online Cash',
                'bank_name' => 'Uang Tunai',
                'cabang' => null,
                'no_rekening' => '-',
                'is_cash' => true,
                'opening_balance' => 5000000, // Rp 5 juta
                'opening_balance_date' => Carbon::parse('2024-01-01'),
            ],
        ];

        foreach ($paymentMethods as $methodData) {
            PaymentMethod::create($methodData);
        }

        $this->command->info('✅ PaymentMethodSeeder completed! Created '.count($paymentMethods).' payment methods.');
    }
}
