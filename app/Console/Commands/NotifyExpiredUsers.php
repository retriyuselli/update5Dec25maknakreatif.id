<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\UserAccountExpired;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyExpiredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:notify-expired {--days=1 : Days since expiration to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users whose accounts have already expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Sending expired account notifications for users expired within last {$days} day(s)...");

        // Get users who expired within the specified days
        $expiredUsers = User::whereBetween('expire_date', [
            Carbon::now()->subDays($days)->startOfDay(),
            Carbon::now()->endOfDay(),
        ])->where('expire_date', '<', Carbon::now())->get();

        if ($expiredUsers->isEmpty()) {
            $this->info('No expired users found within the specified time frame.');

            return 0;
        }

        $this->info("Found {$expiredUsers->count()} expired user(s):");

        foreach ($expiredUsers as $user) {
            $daysSinceExpiration = Carbon::now()->diffInDays($user->expire_date, false);

            // Send notification
            $user->notify(new UserAccountExpired($user));

            $this->line("- Notification sent to {$user->name} ({$user->email}) - Expired {$daysSinceExpiration} day(s) ago");
        }

        $this->info('Expired account notifications sent successfully!');

        return 0;
    }
}
