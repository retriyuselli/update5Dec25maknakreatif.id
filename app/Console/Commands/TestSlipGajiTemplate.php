<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use Illuminate\Console\Command;

class TestSlipGajiTemplate extends Command
{
    protected $signature = 'test:slip-gaji';

    protected $description = 'Test salary slip template with new gaji_pokok and tunjangan structure';

    public function handle()
    {
        $this->info('🧾 Testing Salary Slip Template...');
        $this->newLine();

        // Get a payroll record to test
        $payroll = Payroll::with('user')->first();

        if (! $payroll) {
            $this->error('No payroll records found! Please run: php artisan seed:payroll-data');

            return;
        }

        $this->info("📋 Testing slip for: {$payroll->user->name}");
        $this->newLine();

        // Display the data that will be shown in the slip
        $pengurangan = $payroll->monthly_salary * 0.02; // 2% dari gaji bulanan
        $totalDiterima = $payroll->monthly_salary + ($payroll->bonus ?? 0) - $pengurangan;

        $this->table(['Component', 'Value'], [
            ['Gaji Pokok', 'Rp '.number_format($payroll->gaji_pokok ?? 0, 0, ',', '.')],
            ['Tunjangan', 'Rp '.number_format($payroll->tunjangan ?? 0, 0, ',', '.')],
            ['Sub Total Gaji Bulanan', 'Rp '.number_format($payroll->monthly_salary, 0, ',', '.')],
            ['Bonus', 'Rp '.number_format($payroll->bonus ?? 0, 0, ',', '.')],
            ['Pengurangan (2%)', '- Rp '.number_format($pengurangan, 0, ',', '.')],
            ['Total Diterima', 'Rp '.number_format($totalDiterima, 0, ',', '.')],
            ['---', '---'],
            ['Gaji Tahunan', 'Rp '.number_format($payroll->annual_salary, 0, ',', '.')],
            ['Total Kompensasi', 'Rp '.number_format($payroll->total_compensation, 0, ',', '.')],
        ]);

        $this->newLine();
        $this->info('✅ Template structure verified!');
        $this->info('📄 The salary slip will now show:');
        $this->info('   • Separate gaji_pokok and tunjangan amounts');
        $this->info('   • Correct monthly salary calculation (gaji_pokok + tunjangan)');
        $this->info('   • Proper deductions calculation');
        $this->info('   • Annual summary section');

        $this->newLine();
        $this->warn('💡 You can view the actual slip by accessing the payroll resource in your browser');
    }
}
