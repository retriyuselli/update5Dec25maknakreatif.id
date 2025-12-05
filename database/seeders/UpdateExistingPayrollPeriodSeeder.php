<?php

namespace Database\Seeders;

use App\Models\Payroll;
use Illuminate\Database\Seeder;

class UpdateExistingPayrollPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing payroll records yang tidak memiliki period_month dan period_year
        $payrolls = Payroll::where('period_month', 0)
            ->orWhere('period_year', 0)
            ->orWhereNull('period_month')
            ->orWhereNull('period_year')
            ->get();

        foreach ($payrolls as $payroll) {
            // Set periode berdasarkan created_at
            $createdAt = $payroll->created_at;

            $payroll->update([
                'period_month' => $createdAt->month,
                'period_year' => $createdAt->year,
            ]);

            $this->command->info("Updated Payroll ID {$payroll->id} - Period: {$createdAt->format('F Y')}");
        }

        $this->command->info('Total updated: '.$payrolls->count().' payroll records');
    }
}
