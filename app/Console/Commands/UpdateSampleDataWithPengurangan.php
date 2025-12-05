<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use Illuminate\Console\Command;

class UpdateSampleDataWithPengurangan extends Command
{
    protected $signature = 'update:sample-pengurangan';

    protected $description = 'Update existing sample data with pengurangan values';

    public function handle()
    {
        $this->info('🔄 Updating existing payroll data with pengurangan...');
        $this->newLine();

        $payrolls = Payroll::with('user')->get();

        if ($payrolls->isEmpty()) {
            $this->error('No payroll records found!');

            return;
        }

        $pengurangan_amounts = [100000, 150000, 200000, 80000, 120000]; // Sample deduction amounts
        $updated = 0;

        foreach ($payrolls as $index => $payroll) {
            $pengurangan = $pengurangan_amounts[$index % count($pengurangan_amounts)];

            $payroll->update([
                'pengurangan' => $pengurangan,
            ]);

            $this->info("✅ Updated {$payroll->user->name} with pengurangan: Rp ".number_format($pengurangan, 0, ',', '.'));
            $updated++;
        }

        $this->newLine();
        $this->info("🎉 Successfully updated {$updated} payroll records!");

        // Show updated summary
        $this->newLine();
        $this->info('📊 Updated Payroll Summary:');
        $payrolls = Payroll::with('user')->get();

        $tableData = [];
        foreach ($payrolls as $payroll) {
            $tableData[] = [
                $payroll->user->name,
                'Rp '.number_format($payroll->gaji_pokok, 0, ',', '.'),
                'Rp '.number_format($payroll->tunjangan, 0, ',', '.'),
                'Rp '.number_format($payroll->pengurangan, 0, ',', '.'),
                'Rp '.number_format($payroll->monthly_salary, 0, ',', '.'),
                'Rp '.number_format($payroll->total_compensation, 0, ',', '.'),
            ];
        }

        $this->table(
            ['Name', 'Gaji Pokok', 'Tunjangan', 'Pengurangan', 'Total Gaji', 'Total Kompensasi'],
            $tableData
        );
    }
}
