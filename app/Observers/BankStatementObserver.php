<?php

namespace App\Observers;

use App\Models\BankStatement;
use Illuminate\Support\Facades\Auth;

class BankStatementObserver
{
    /**
     * Handle the BankStatement "creating" event.
     */
    public function creating(BankStatement $bankStatement): void
    {
        if (Auth::check()) {
            $bankStatement->uploaded_by = Auth::id();
            $bankStatement->last_edited_by = Auth::id();
        }
    }

    /**
     * Handle the BankStatement "updating" event.
     */
    public function updating(BankStatement $bankStatement): void
    {
        if (Auth::check()) {
            $bankStatement->last_edited_by = Auth::id();
        }
    }

    /**
     * Handle the BankStatement "deleted" event.
     */
    public function deleted(BankStatement $bankStatement): void
    {
        //
    }

    /**
     * Handle the BankStatement "restored" event.
     */
    public function restored(BankStatement $bankStatement): void
    {
        //
    }

    /**
     * Handle the BankStatement "force deleted" event.
     */
    public function forceDeleted(BankStatement $bankStatement): void
    {
        //
    }
}
