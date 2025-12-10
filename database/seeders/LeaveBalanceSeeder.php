<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating Leave Balances for all users...');
        $result = LeaveBalance::generateForAllUsers(now()->year);
        $this->command->info($result['message']);
    }
}

