<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Clear existing payroll data
        Payroll::truncate();

        // Get all users for payroll assignment
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found! Please run UserSeeder first.');

            return;
        }

        $this->command->info('🏢 Creating Payroll Data...');

        // Sample payroll data with realistic Indonesian salary ranges
        $payrollData = [
            // Management Level
            [
                'position_level' => 'Executive',
                'monthly_salary_range' => [15000000, 25000000],
                'bonus_range' => [5000000, 10000000],
                'status_filter' => ['Admin', 'Finance', 'HRD'],
            ],

            // Manager Level
            [
                'position_level' => 'Manager',
                'monthly_salary_range' => [8000000, 15000000],
                'bonus_range' => [2000000, 5000000],
                'status_filter' => ['Account Manager'],
            ],

            // Staff Level
            [
                'position_level' => 'Staff',
                'monthly_salary_range' => [4000000, 8000000],
                'bonus_range' => [500000, 2000000],
                'status_filter' => ['Staff'],
            ],
        ];

        $createdCount = 0;

        foreach ($users as $user) {
            // Determine salary range based on user status
            $salaryData = $this->getSalaryDataByStatus($user, $payrollData);

            // Generate random salary within range
            $monthlySalary = rand($salaryData['monthly_salary_range'][0], $salaryData['monthly_salary_range'][1]);
            $bonus = rand($salaryData['bonus_range'][0], $salaryData['bonus_range'][1]);

            // Generate review dates
            $lastReviewDate = Carbon::now()->subMonths(rand(1, 12));
            $nextReviewDate = Carbon::now()->addMonths(rand(1, 6));

            // Create realistic notes
            $notes = $this->generateRealisticNotes($user, $monthlySalary, $salaryData['position_level']);

            $payroll = Payroll::create([
                'user_id' => $user->id,
                'monthly_salary' => $monthlySalary,
                'bonus' => $bonus,
                'last_review_date' => $lastReviewDate,
                'next_review_date' => $nextReviewDate,
                'notes' => $notes,
            ]);

            $this->command->line("✅ Created payroll for {$user->name}:");
            $this->command->line('   💰 Monthly: Rp '.number_format($monthlySalary, 0, ',', '.'));
            $this->command->line('   📅 Annual: Rp '.number_format($payroll->annual_salary, 0, ',', '.'));
            $this->command->line('   🎁 Bonus: Rp '.number_format($bonus, 0, ',', '.'));
            $this->command->line('   💵 Total: Rp '.number_format($payroll->total_compensation, 0, ',', '.'));
            $statusDisplay = $user->status?->status_name ?? $user->department ?? 'No Status';
            $this->command->line("   📝 Status: {$statusDisplay}");
            $this->command->line('');

            $createdCount++;
        }

        $this->command->info("🎉 Successfully created {$createdCount} payroll records!");

        // Show summary statistics
        $this->showPayrollSummary();
    }

    private function getSalaryDataByStatus($user, $payrollData)
    {
        $userStatus = $user->status?->status_name ?? $user->department ?? 'Staff';

        foreach ($payrollData as $data) {
            if (in_array($userStatus, $data['status_filter'])) {
                return $data;
            }
        }

        // Default to staff level if no match
        return $payrollData[2]; // Staff level
    }

    private function generateRealisticNotes($user, $monthlySalary, $positionLevel)
    {
        $notes = [];

        // Add initial hire note
        $hireDate = $user->hire_date ?? Carbon::now()->subYears(rand(1, 5));
        $notes[] = "[{$hireDate->format('d/m/Y')}] Gaji awal: Rp ".number_format($monthlySalary * 0.8, 0, ',', '.')." - Posisi: {$positionLevel}";

        // Add performance review notes
        $reviewComments = [
            'Review tahunan: Kinerja excellent, kenaikan 15%',
            'Promosi jabatan: Dari staff menjadi '.strtolower($positionLevel),
            'Penyesuaian gaji sesuai inflasi dan market rate',
            'Bonus achievement: Target tercapai 110%',
            'Sertifikasi profesional: Menambah kompetensi',
        ];

        $randomReviewDate = Carbon::now()->subMonths(rand(6, 18));
        $notes[] = "[{$randomReviewDate->format('d/m/Y')}] ".$reviewComments[array_rand($reviewComments)];

        // Add current salary note
        $notes[] = '['.Carbon::now()->format('d/m/Y').'] Gaji saat ini: Rp '.number_format($monthlySalary, 0, ',', '.').' - Sistem payroll terintegrasi';

        return implode("\n\n", $notes);
    }

    private function showPayrollSummary()
    {
        $this->command->info('📊 PAYROLL SUMMARY:');
        $this->command->line('─────────────────────────────────────');

        $totalPayrolls = Payroll::count();
        $totalMonthlySalary = Payroll::sum('monthly_salary');
        $totalAnnualSalary = Payroll::sum('annual_salary');
        $totalBonus = Payroll::sum('bonus');
        $avgMonthlySalary = Payroll::avg('monthly_salary');

        $this->command->line("📈 Total Employees: {$totalPayrolls}");
        $this->command->line('💰 Total Monthly Payroll: Rp '.number_format($totalMonthlySalary, 0, ',', '.'));
        $this->command->line('📅 Total Annual Payroll: Rp '.number_format($totalAnnualSalary, 0, ',', '.'));
        $this->command->line('🎁 Total Bonus Pool: Rp '.number_format($totalBonus, 0, ',', '.'));
        $this->command->line('📊 Average Monthly Salary: Rp '.number_format($avgMonthlySalary, 0, ',', '.'));
        $this->command->line('💵 Total Annual Compensation: Rp '.number_format($totalAnnualSalary + $totalBonus, 0, ',', '.'));

        // Show breakdown by status (simplified version)
        $this->command->line('');
        $this->command->info('📋 BREAKDOWN BY STATUS:');

        // Simple approach using Eloquent to avoid GROUP BY issues
        $payrollsWithUsers = Payroll::with(['user.status'])->get()->groupBy(function ($payroll) {
            return $payroll->user->status?->status_name ?? $payroll->user->department ?? 'No Status';
        });

        foreach ($payrollsWithUsers as $statusName => $payrolls) {
            $employeeCount = $payrolls->count();
            $avgMonthly = $payrolls->avg('monthly_salary');
            $totalCompensation = $payrolls->sum(function ($payroll) {
                return $payroll->annual_salary + $payroll->bonus;
            });

            $this->command->line("└─ {$statusName}: {$employeeCount} employees");
            $this->command->line('   Average Monthly: Rp '.number_format($avgMonthly, 0, ',', '.'));
            $this->command->line('   Total Compensation: Rp '.number_format($totalCompensation, 0, ',', '.'));
            $this->command->line('');
        }
    }
}
