<?php

namespace App\Console\Commands;

use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateLeaveBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:generate-balances 
                            {--user-id= : Generate for specific user ID}
                            {--year= : Generate for specific year (default: current year)}
                            {--force : Force regenerate all records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate leave balances for all users based on leave types and user quotas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting leave balance generation...');

        $userId = $this->option('user-id');
        $year = $this->option('year') ?? now()->year;
        $force = $this->option('force');

        if ($userId) {
            // Generate for specific user
            $user = User::find($userId);
            if (! $user) {
                $this->error("❌ User with ID {$userId} not found!");

                return 1;
            }

            $this->info("📊 Generating leave balances for user: {$user->name}");
            $result = LeaveBalance::generateForUser($user, $year);
        } else {
            // Generate for all users
            $this->info("📊 Generating leave balances for all users (Year: {$year})");
            $result = LeaveBalance::generateForAllUsers($year);
        }

        // Display results
        $this->newLine();
        $this->info('✅ Generation completed!');
        $this->line("📈 Created: {$result['created']} new records");
        $this->line("🔄 Updated: {$result['updated']} existing records");
        $this->line("💬 {$result['message']}");

        return 0;
    }
}
