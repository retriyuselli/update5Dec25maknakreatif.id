<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Console\Command;

class TestPengurangan extends Command
{
    protected $signature = 'test:pengurangan';

    protected $description = 'Test pengurangan functionality in payroll';

    public function handle()
    {
        $this->info('🧪 Testing Pengurangan (Deductions) Functionality...');
        $this->newLine();

        // Test dengan data sample
        $user = User::first();
        if (! $user) {
            $this->error('No users found!');

            return;
        }

        $testPayroll = new Payroll([
            'user_id' => $user->id,
            'gaji_pokok' => 5000000,
            'tunjangan' => 1500000,
            'pengurangan' => 200000, // BPJS, keterlambatan, dll
            'bonus' => 750000,
            'period_month' => 11,
            'period_year' => 2024,
        ]);

        $testPayroll->save();

        $this->info("📋 Testing payroll for: {$user->name}");
        $this->newLine();

        // Verify calculations
        $expectedMonthlySalary = 5000000 + 1500000 - 200000; // 6,300,000
        $expectedAnnualSalary = $expectedMonthlySalary * 12; // 75,600,000
        $expectedTotalCompensation = $expectedAnnualSalary + 750000; // 76,350,000

        $this->table(['Component', 'Expected', 'Actual', 'Status'], [
            [
                'Gaji Pokok',
                'Rp '.number_format(5000000, 0, ',', '.'),
                'Rp '.number_format($testPayroll->gaji_pokok, 0, ',', '.'),
                $testPayroll->gaji_pokok == 5000000 ? '✅' : '❌',
            ],
            [
                'Tunjangan',
                'Rp '.number_format(1500000, 0, ',', '.'),
                'Rp '.number_format($testPayroll->tunjangan, 0, ',', '.'),
                $testPayroll->tunjangan == 1500000 ? '✅' : '❌',
            ],
            [
                'Pengurangan',
                'Rp '.number_format(200000, 0, ',', '.'),
                'Rp '.number_format($testPayroll->pengurangan, 0, ',', '.'),
                $testPayroll->pengurangan == 200000 ? '✅' : '❌',
            ],
            [
                'Monthly Salary',
                'Rp '.number_format($expectedMonthlySalary, 0, ',', '.'),
                'Rp '.number_format($testPayroll->monthly_salary, 0, ',', '.'),
                $testPayroll->monthly_salary == $expectedMonthlySalary ? '✅' : '❌',
            ],
            [
                'Annual Salary',
                'Rp '.number_format($expectedAnnualSalary, 0, ',', '.'),
                'Rp '.number_format($testPayroll->annual_salary, 0, ',', '.'),
                $testPayroll->annual_salary == $expectedAnnualSalary ? '✅' : '❌',
            ],
            [
                'Total Compensation',
                'Rp '.number_format($expectedTotalCompensation, 0, ',', '.'),
                'Rp '.number_format($testPayroll->total_compensation, 0, ',', '.'),
                $testPayroll->total_compensation == $expectedTotalCompensation ? '✅' : '❌',
            ],
        ]);

        $allCorrect = (
            $testPayroll->monthly_salary == $expectedMonthlySalary &&
            $testPayroll->annual_salary == $expectedAnnualSalary &&
            $testPayroll->total_compensation == $expectedTotalCompensation
        );

        $this->newLine();
        if ($allCorrect) {
            $this->info('🎉 All calculations are correct!');
            $this->info('✨ Formula: Monthly Salary = Gaji Pokok + Tunjangan - Pengurangan');
            $this->info('📊 Calculation: 5,000,000 + 1,500,000 - 200,000 = 6,300,000');
        } else {
            $this->error('❌ Some calculations are incorrect!');
        }

        // Clean up
        $testPayroll->delete();
        $this->newLine();
        $this->info('🧹 Test record cleaned up.');
    }
}
