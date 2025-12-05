<?php

namespace App\Console\Commands;

use App\Jobs\LogoutExpiredUsers;
use Illuminate\Console\Command;

class ForceLogoutExpiredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:force-logout-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force logout all expired users immediately';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting force logout process for expired users...');

        // Dispatch the job synchronously for immediate execution
        $job = new LogoutExpiredUsers;
        $job->handle();

        $this->info('Force logout process completed!');
        $this->line('Check logs for detailed information.');

        return 0;
    }
}
