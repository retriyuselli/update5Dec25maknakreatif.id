<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExpiredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-expired {--force : Force logout all expired users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired users and optionally force logout';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired users...');

        // Get expired users
        $expiredUsers = User::where('expire_date', '<=', Carbon::now())
            ->whereNotNull('expire_date')
            ->get();

        if ($expiredUsers->isEmpty()) {
            $this->info('No expired users found.');

            return 0;
        }

        $this->info("Found {$expiredUsers->count()} expired user(s):");

        foreach ($expiredUsers as $user) {
            $this->line("- {$user->name} ({$user->email}) - Expired: {$user->expire_date}");
        }

        if ($this->option('force')) {
            $this->info('Force logging out expired users...');

            // Force logout by clearing sessions for expired users
            $expiredUserIds = $expiredUsers->pluck('id')->toArray();

            // Clear sessions for expired users (if using database sessions)
            DB::table('sessions')
                ->whereIn('user_id', $expiredUserIds)
                ->delete();

            $this->info('Expired users have been logged out.');
        } else {
            $this->info('Use --force flag to force logout expired users.');
        }

        // Check users expiring soon
        $expiringSoonUsers = User::whereBetween('expire_date', [
            Carbon::now(),
            Carbon::now()->addDays(7),
        ])->get();

        if ($expiringSoonUsers->isNotEmpty()) {
            $this->warn('Users expiring within 7 days:');
            foreach ($expiringSoonUsers as $user) {
                $days = Carbon::now()->diffInDays($user->expire_date);
                $this->line("- {$user->name} ({$user->email}) - Expires in {$days} day(s)");
            }
        }

        return 0;
    }
}
