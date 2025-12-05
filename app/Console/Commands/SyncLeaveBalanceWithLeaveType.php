<?php

namespace App\Console\Commands;

use App\Models\LeaveBalance;
use Illuminate\Console\Command;

class SyncLeaveBalanceWithLeaveType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:sync-allocated-days {--force : Force update even if allocated_days already set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync leave balance allocated_days with LeaveType max_days_per_year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing Leave Balance allocated_days with LeaveType max_days_per_year...');

        $force = $this->option('force');
        $updated = 0;
        $skipped = 0;

        $leaveBalances = LeaveBalance::with('leaveType')->get();

        foreach ($leaveBalances as $balance) {
            if (! $balance->leaveType) {
                $this->warn("⚠️  LeaveBalance ID {$balance->id} has no associated LeaveType");
                $skipped++;

                continue;
            }

            $newAllocatedDays = $balance->leaveType->max_days_per_year;

            // Skip if already correct and not forcing
            if (! $force && $balance->allocated_days == $newAllocatedDays) {
                $skipped++;

                continue;
            }

            $oldDays = $balance->allocated_days;
            $balance->allocated_days = $newAllocatedDays;
            $balance->remaining_days = $balance->allocated_days - $balance->used_days;

            // Debug: Cek apakah save berhasil
            $saved = $balance->save();
            $balance->refresh(); // Refresh untuk memastikan data dari database

            if ($saved && $balance->allocated_days == $newAllocatedDays) {
                $this->line("✅ Updated {$balance->user->name} - {$balance->leaveType->name}: {$oldDays} → {$newAllocatedDays} days");
                $updated++;
            } else {
                $this->error("❌ Failed to update {$balance->user->name} - {$balance->leaveType->name}: {$oldDays} → {$newAllocatedDays} days (actual: {$balance->allocated_days})");
            }
        }

        $this->info('🎉 Sync completed!');
        $this->info("📊 Updated: {$updated} records");
        $this->info("⏭️  Skipped: {$skipped} records");

        return Command::SUCCESS;
    }
}
