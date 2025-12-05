<?php

namespace Database\Seeders;

use App\Models\AccountManagerTarget;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountManagerTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing targets first
        AccountManagerTarget::truncate();

        // Data Account Manager dengan target masing-masing
        $accountManagerData = [
            'Rama Dhona Utama' => [
                ['year' => 2025, 'month' => 1, 'target' => 1500000000, 'achieved' => 120000000],
                ['year' => 2025, 'month' => 2, 'target' => 1500000000, 'achieved' => 178937062],
                ['year' => 2025, 'month' => 3, 'target' => 1500000000, 'achieved' => 205072793],
                ['year' => 2025, 'month' => 4, 'target' => 1500000000, 'achieved' => 95000000],
                ['year' => 2025, 'month' => 5, 'target' => 1500000000, 'achieved' => 85000000],
                ['year' => 2025, 'month' => 6, 'target' => 1500000000, 'achieved' => 38392520],
                ['year' => 2025, 'month' => 7, 'target' => 1500000000, 'achieved' => 110000000],
                ['year' => 2025, 'month' => 8, 'target' => 1500000000, 'achieved' => 95000000],
                ['year' => 2025, 'month' => 9, 'target' => 1500000000, 'achieved' => 123049202],
                ['year' => 2025, 'month' => 10, 'target' => 1500000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 11, 'target' => 1500000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 12, 'target' => 1500000000, 'achieved' => 0],
            ],
            'Rina Mardiana' => [
                ['year' => 2025, 'month' => 1, 'target' => 1200000000, 'achieved' => 95000000],
                ['year' => 2025, 'month' => 2, 'target' => 1200000000, 'achieved' => 110000000],
                ['year' => 2025, 'month' => 3, 'target' => 1200000000, 'achieved' => 125000000],
                ['year' => 2025, 'month' => 4, 'target' => 1200000000, 'achieved' => 140000000],
                ['year' => 2025, 'month' => 5, 'target' => 1200000000, 'achieved' => 155000000],
                ['year' => 2025, 'month' => 6, 'target' => 1200000000, 'achieved' => 170000000],
                ['year' => 2025, 'month' => 7, 'target' => 1200000000, 'achieved' => 261795873],
                ['year' => 2025, 'month' => 8, 'target' => 1200000000, 'achieved' => 190000000],
                ['year' => 2025, 'month' => 9, 'target' => 1200000000, 'achieved' => 205000000],
                ['year' => 2025, 'month' => 10, 'target' => 1200000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 11, 'target' => 1200000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 12, 'target' => 1200000000, 'achieved' => 0],
            ],
            'Adel' => [
                ['year' => 2025, 'month' => 1, 'target' => 1000000000, 'achieved' => 75000000],
                ['year' => 2025, 'month' => 2, 'target' => 1000000000, 'achieved' => 85000000],
                ['year' => 2025, 'month' => 3, 'target' => 1000000000, 'achieved' => 95000000],
                ['year' => 2025, 'month' => 4, 'target' => 1000000000, 'achieved' => 177674603],
                ['year' => 2025, 'month' => 5, 'target' => 1000000000, 'achieved' => 115000000],
                ['year' => 2025, 'month' => 6, 'target' => 1000000000, 'achieved' => 117126796],
                ['year' => 2025, 'month' => 7, 'target' => 1000000000, 'achieved' => 135000000],
                ['year' => 2025, 'month' => 8, 'target' => 1000000000, 'achieved' => 70936487],
                ['year' => 2025, 'month' => 9, 'target' => 1000000000, 'achieved' => 155000000],
                ['year' => 2025, 'month' => 10, 'target' => 1000000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 11, 'target' => 1000000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 12, 'target' => 1000000000, 'achieved' => 0],
            ],
            'Sari Ananda' => [
                ['year' => 2025, 'month' => 1, 'target' => 800000000, 'achieved' => 65000000],
                ['year' => 2025, 'month' => 2, 'target' => 800000000, 'achieved' => 72000000],
                ['year' => 2025, 'month' => 3, 'target' => 800000000, 'achieved' => 68000000],
                ['year' => 2025, 'month' => 4, 'target' => 800000000, 'achieved' => 85000000],
                ['year' => 2025, 'month' => 5, 'target' => 800000000, 'achieved' => 78000000],
                ['year' => 2025, 'month' => 6, 'target' => 800000000, 'achieved' => 92000000],
                ['year' => 2025, 'month' => 7, 'target' => 800000000, 'achieved' => 88000000],
                ['year' => 2025, 'month' => 8, 'target' => 800000000, 'achieved' => 95000000],
                ['year' => 2025, 'month' => 9, 'target' => 800000000, 'achieved' => 105000000],
                ['year' => 2025, 'month' => 10, 'target' => 800000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 11, 'target' => 800000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 12, 'target' => 800000000, 'achieved' => 0],
            ],
            'Devi Kartika' => [
                ['year' => 2025, 'month' => 1, 'target' => 900000000, 'achieved' => 58000000],
                ['year' => 2025, 'month' => 2, 'target' => 900000000, 'achieved' => 63000000],
                ['year' => 2025, 'month' => 3, 'target' => 900000000, 'achieved' => 71000000],
                ['year' => 2025, 'month' => 4, 'target' => 900000000, 'achieved' => 79000000],
                ['year' => 2025, 'month' => 5, 'target' => 900000000, 'achieved' => 87000000],
                ['year' => 2025, 'month' => 6, 'target' => 900000000, 'achieved' => 94000000],
                ['year' => 2025, 'month' => 7, 'target' => 900000000, 'achieved' => 102000000],
                ['year' => 2025, 'month' => 8, 'target' => 900000000, 'achieved' => 108000000],
                ['year' => 2025, 'month' => 9, 'target' => 900000000, 'achieved' => 115000000],
                ['year' => 2025, 'month' => 10, 'target' => 900000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 11, 'target' => 900000000, 'achieved' => 0],
                ['year' => 2025, 'month' => 12, 'target' => 900000000, 'achieved' => 0],
            ],
        ];

        $created = 0;
        foreach ($accountManagerData as $userName => $periods) {
            // Find user by name
            $user = User::where('name', $userName)->first();

            if (! $user) {
                $this->command->warn("User {$userName} not found, skipping...");

                continue;
            }

            foreach ($periods as $period) {
                $status = 'pending';
                $percentage = $period['target'] > 0 ? ($period['achieved'] / $period['target']) * 100 : 0;

                if ($percentage > 100) {
                    $status = 'overachieved';
                } elseif ($percentage >= 100) {
                    $status = 'achieved';
                } elseif ($percentage >= 50) {
                    $status = 'partially_achieved';
                } else {
                    $status = 'failed';
                }

                AccountManagerTarget::create([
                    'user_id' => $user->id,
                    'year' => $period['year'],
                    'month' => $period['month'],
                    'target_amount' => $period['target'],
                    'achieved_amount' => $period['achieved'],
                    'status' => $status,
                ]);

                $created++;
            }
        }

        $this->command->info("✅ Created: {$created} Account Manager targets");
        $this->command->info('📊 Total targets now: '.AccountManagerTarget::count());

        // Show summary
        $this->command->newLine();
        $this->command->info('📈 Account Manager Performance Summary:');

        $summary = AccountManagerTarget::selectRaw('
            users.name,
            SUM(target_amount) as total_target,
            SUM(achieved_amount) as total_achieved,
            ROUND((SUM(achieved_amount) / SUM(target_amount)) * 100, 2) as overall_percentage
        ')
            ->join('users', 'users.id', '=', 'account_manager_targets.user_id')
            ->where('year', 2025)
            ->groupBy('user_id', 'users.name')
            ->orderByDesc('overall_percentage')
            ->get();

        foreach ($summary as $item) {
            $this->command->line("• {$item->name}: ".number_format($item->total_achieved).' / '.number_format($item->total_target)." ({$item->overall_percentage}%)");
        }
    }
}
