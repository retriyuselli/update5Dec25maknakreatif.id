<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Console\Command;

class TestPayrollSalaryCalculation extends Command
{
    protected $signature = 'test:payroll-calculation';

    protected $description = 'Test gaji_pokok + tunjangan = monthly_salary calculation';

    public function handle()
    {
        $this->info('🧪 Testing Payroll Salary Calculation...');
        $this->newLine();

        // Test 1: Create a new payroll with gaji_pokok and tunjangan
        $this->info('Test 1: Creating new payroll with gaji_pokok and tunjangan');

        $user = User::first();
        if (! $user) {
            $this->error('No users found! Please create a user first.');

            return;
        }

        $testPayroll = new Payroll([
            'user_id' => $user->id,
            'gaji_pokok' => 4000000,
            'tunjangan' => 1000000,
            'bonus' => 500000,
            'period_month' => now()->month,
            'period_year' => now()->year,
        ]);

        $testPayroll->save();

        $this->table(['Field', 'Value'], [
            ['User', $user->name],
            ['Gaji Pokok', 'Rp '.number_format($testPayroll->gaji_pokok, 0, ',', '.')],
            ['Tunjangan', 'Rp '.number_format($testPayroll->tunjangan, 0, ',', '.')],
            ['Monthly Salary (Calculated)', 'Rp '.number_format($testPayroll->monthly_salary, 0, ',', '.')],
            ['Annual Salary', 'Rp '.number_format($testPayroll->annual_salary, 0, ',', '.')],
            ['Bonus', 'Rp '.number_format($testPayroll->bonus, 0, ',', '.')],
            ['Total Compensation', 'Rp '.number_format($testPayroll->total_compensation, 0, ',', '.')],
        ]);

        // Verify calculation
        $expectedMonthlySalary = $testPayroll->gaji_pokok + $testPayroll->tunjangan;
        $expectedAnnualSalary = $expectedMonthlySalary * 12;
        $expectedTotalCompensation = $expectedAnnualSalary + $testPayroll->bonus;

        $this->newLine();
        $this->info('🔍 Verification:');

        if ($testPayroll->monthly_salary == $expectedMonthlySalary) {
            $this->info('✅ Monthly salary calculation: CORRECT');
        } else {
            $this->error('❌ Monthly salary calculation: FAILED');
            $this->error("Expected: {$expectedMonthlySalary}, Got: {$testPayroll->monthly_salary}");
        }

        if ($testPayroll->annual_salary == $expectedAnnualSalary) {
            $this->info('✅ Annual salary calculation: CORRECT');
        } else {
            $this->error('❌ Annual salary calculation: FAILED');
            $this->error("Expected: {$expectedAnnualSalary}, Got: {$testPayroll->annual_salary}");
        }

        if ($testPayroll->total_compensation == $expectedTotalCompensation) {
            $this->info('✅ Total compensation calculation: CORRECT');
        } else {
            $this->error('❌ Total compensation calculation: FAILED');
            $this->error("Expected: {$expectedTotalCompensation}, Got: {$testPayroll->total_compensation}");
        }

        // Clean up
        $testPayroll->delete();
        $this->newLine();
        $this->info('🧹 Test payroll record cleaned up.');
        $this->info('✨ Test completed successfully!');
    }
}
