<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JournalBatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalBatchPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JournalBatch');
    }

    public function view(AuthUser $authUser, JournalBatch $journalBatch): bool
    {
        return $authUser->can('View:JournalBatch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JournalBatch');
    }

    public function update(AuthUser $authUser, JournalBatch $journalBatch): bool
    {
        return $authUser->can('Update:JournalBatch');
    }

    public function delete(AuthUser $authUser, JournalBatch $journalBatch): bool
    {
        return $authUser->can('Delete:JournalBatch');
    }

    public function restore(AuthUser $authUser, JournalBatch $journalBatch): bool
    {
        return $authUser->can('Restore:JournalBatch');
    }

    public function forceDelete(AuthUser $authUser, JournalBatch $journalBatch): bool
    {
        return $authUser->can('ForceDelete:JournalBatch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JournalBatch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JournalBatch');
    }

    public function replicate(AuthUser $authUser, JournalBatch $journalBatch): bool
    {
        return $authUser->can('Replicate:JournalBatch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JournalBatch');
    }

}