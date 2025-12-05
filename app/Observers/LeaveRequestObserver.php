<?php

namespace App\Observers;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;

class LeaveRequestObserver
{
    /**
     * Handle the LeaveRequest "creating" event.
     */
    public function creating(LeaveRequest $leaveRequest): void
    {
        // Pastikan user_id selalu diisi saat creating
        if (empty($leaveRequest->user_id) && Auth::check()) {
            $leaveRequest->user_id = Auth::id();
        }
    }

    /**
     * Handle the LeaveRequest "created" event.
     */
    public function created(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "updated" event.
     */
    public function updated(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "deleted" event.
     */
    public function deleted(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "restored" event.
     */
    public function restored(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "force deleted" event.
     */
    public function forceDeleted(LeaveRequest $leaveRequest): void
    {
        //
    }
}
