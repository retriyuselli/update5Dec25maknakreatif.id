<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        // Only update if user is authenticated
        if (Auth::check()) {
            $order->last_edited_by = Auth::id();
        }
    }

    /**
     * Handle the Order "creating" event.
     */
    public function creating(Order $order): void
    {
        // Set initial last_edited_by when creating
        if (Auth::check()) {
            $order->last_edited_by = Auth::id();
        }
    }
}
