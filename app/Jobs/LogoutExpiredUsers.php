<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogoutExpiredUsers implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting logout process for expired users');

        // Get expired users
        $expiredUsers = User::where('expire_date', '<=', Carbon::now())
            ->whereNotNull('expire_date')
            ->get();

        if ($expiredUsers->isEmpty()) {
            Log::info('No expired users found');

            return;
        }

        Log::info("Found {$expiredUsers->count()} expired user(s)");

        $expiredUserIds = $expiredUsers->pluck('id')->toArray();

        // Clear sessions for expired users (if using database sessions)
        $deletedSessions = DB::table('sessions')
            ->whereIn('user_id', $expiredUserIds)
            ->delete();

        // Clear personal access tokens if using Sanctum
        if (class_exists('Laravel\Sanctum\PersonalAccessToken')) {
            DB::table('personal_access_tokens')
                ->whereIn('tokenable_id', $expiredUserIds)
                ->where('tokenable_type', 'App\Models\User')
                ->delete();
        }

        Log::info("Logged out {$expiredUsers->count()} expired users. Deleted {$deletedSessions} sessions.");

        // Optionally, you can also disable the users
        // User::whereIn('id', $expiredUserIds)->update(['status_id' => $disabledStatusId]);
    }
}
