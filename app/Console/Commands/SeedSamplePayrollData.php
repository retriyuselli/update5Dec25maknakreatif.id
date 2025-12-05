<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Console\Command;

class SeedSamplePayrollData extends Command
{
    protected $signature = 'seed:payroll-data';

    protected $description = 'Seed sample payroll data with gaji_pokok and tunjangan';

    public function handle()
    {
        $this->info('🌱 Seeding sample payroll data...');
        $this->newLine();

        $users = User::all();
        if ($users->isEmpty()) {
            $this->error('No users found! Please create users first.');

            return;
        }

        $sampleData = [
            ['gaji_pokok' => 4000000, 'tunjangan' => 1000000, 'bonus' => 500000],
            ['gaji_pokok' => 3500000, 'tunjangan' => 1500000, 'bonus' => 750000],
            ['gaji_pokok' => 5000000, 'tunjangan' => 2000000, 'bonus' => 1000000],
            ['gaji_pokok' => 3000000, 'tunjangan' => 800000, 'bonus' => 300000],
            ['gaji_pokok' => 4500000, 'tunjangan' => 1200000, 'bonus' => 600000],
        ];

        $created = 0;
        foreach ($users->take(5) as $index => $user) {
            if (isset($sampleData[$index])) {
                $data = $sampleData[$index];

                // Check if payroll already exists for this user in current period
                $existing = Payroll::where('user_id', $user->id)
                    ->where('period_month', now()->month)
                    ->where('period_year', now()->year)
                    ->first();

                if (! $existing) {
                    Payroll::create([
                        'user_id' => $user->id,
                        'gaji_pokok' => $data['gaji_pokok'],
                        'tunjangan' => $data['tunjangan'],
                        'bonus' => $data['bonus'],
                        'period_month' => now()->month,
                        'period_year' => now()->year,
                    ]);

                    $created++;
                    $this->info("✅ Created payroll for {$user->name}");
                } else {
                    $this->warn("⚠️ Payroll already exists for {$user->name}");
                }
            }
        }

        $this->newLine();
        $this->info("🎉 Successfully created {$created} payroll records!");

        // Show summary
        $this->newLine();
        $this->info('📊 Payroll Summary:');
        $payrolls = Payroll::with('user')->get();

        $tableData = [];
        foreach ($payrolls as $payroll) {
            $tableData[] = [
                $payroll->user->name,
                'Rp '.number_format($payroll->gaji_pokok, 0, ',', '.'),
                'Rp '.number_format($payroll->tunjangan, 0, ',', '.'),
                'Rp '.number_format($payroll->monthly_salary, 0, ',', '.'),
                'Rp '.number_format($payroll->bonus, 0, ',', '.'),
                'Rp '.number_format($payroll->total_compensation, 0, ',', '.'),
            ];
        }

        $this->table(
            ['Name', 'Gaji Pokok', 'Tunjangan', 'Total Gaji', 'Bonus', 'Total Kompensasi'],
            $tableData
        );
    }
}
