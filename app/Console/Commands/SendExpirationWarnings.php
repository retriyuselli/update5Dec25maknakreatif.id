<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\UserExpirationWarning;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendExpirationWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:send-expiration-warnings {--days=7 : Days before expiration to send warning} {--all : Send warnings for multiple day intervals}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send expiration warnings to users whose accounts will expire soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            // Send warnings for multiple intervals: 30, 14, 7, 3, 1 days
            $intervals = [30, 14, 7, 3, 1];

            $this->info('Sending expiration warnings for multiple intervals...');

            foreach ($intervals as $days) {
                $this->sendWarningsForInterval($days);
            }

        } else {
            $days = (int) $this->option('days');
            $this->sendWarningsForInterval($days);
        }

        return 0;
    }

    /**
     * Send warnings for specific day interval
     */
    private function sendWarningsForInterval(int $days)
    {
        $this->info("Checking users expiring in {$days} days...");

        // Get users expiring within specified days
        $expiringSoonUsers = User::whereBetween('expire_date', [
            Carbon::now()->addDays($days - 1)->startOfDay(),
            Carbon::now()->addDays($days)->endOfDay(),
        ])->get();

        if ($expiringSoonUsers->isEmpty()) {
            $this->line("No users found expiring within {$days} days.");

            return;
        }

        $this->info("Found {$expiringSoonUsers->count()} user(s) expiring within {$days} days:");

        foreach ($expiringSoonUsers as $user) {
            $daysUntilExpiration = Carbon::now()->diffInDays($user->expire_date, false);

            // Send notification
            $user->notify(new UserExpirationWarning($user, $daysUntilExpiration));

            $this->line("- Warning sent to {$user->name} ({$user->email}) - Expires in {$daysUntilExpiration} day(s)");
        }

        $this->info("Expiration warnings for {$days}-day interval sent successfully!");
    }
}
