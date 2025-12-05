<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;

class ClearExpirationNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiration:clear-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expiration notification tracking (for testing)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear session data related to expiration notifications
        $sessionData = session()->all();
        $cleared = 0;

        foreach ($sessionData as $key => $value) {
            if (str_contains($key, 'expiration_warning_shown_') ||
                str_contains($key, 'daily_login_warning_')) {
                session()->forget($key);
                $cleared++;
            }
        }

        $this->info("Cleared {$cleared} expiration notification tracking session(s).");
        $this->line('Users will now receive notifications again:');
        $this->line('- Middleware notification: on next request');
        $this->line('- Login welcome: on next login');

        return 0;
    }
}
