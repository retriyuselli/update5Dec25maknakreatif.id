<?php

namespace App\Observers;

use App\Models\LeaveBalance;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Auto-generate leave balances for new user
        LeaveBalance::generateForUser($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // If annual_leave_quota was updated, regenerate leave balances
        if ($user->isDirty('annual_leave_quota')) {
            LeaveBalance::generateForUser($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Clean up leave balances when user is deleted
        $user->leaveBalances()->delete();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Regenerate leave balances when user is restored
        LeaveBalance::generateForUser($user);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // Clean up leave balances when user is force deleted
        $user->leaveBalances()->delete();
    }
}
