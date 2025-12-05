<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ExpenseOps;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpenseOpsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExpenseOps');
    }

    public function view(AuthUser $authUser, ExpenseOps $expenseOps): bool
    {
        return $authUser->can('View:ExpenseOps');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExpenseOps');
    }

    public function update(AuthUser $authUser, ExpenseOps $expenseOps): bool
    {
        return $authUser->can('Update:ExpenseOps');
    }

    public function delete(AuthUser $authUser, ExpenseOps $expenseOps): bool
    {
        return $authUser->can('Delete:ExpenseOps');
    }

    public function restore(AuthUser $authUser, ExpenseOps $expenseOps): bool
    {
        return $authUser->can('Restore:ExpenseOps');
    }

    public function forceDelete(AuthUser $authUser, ExpenseOps $expenseOps): bool
    {
        return $authUser->can('ForceDelete:ExpenseOps');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExpenseOps');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExpenseOps');
    }

    public function replicate(AuthUser $authUser, ExpenseOps $expenseOps): bool
    {
        return $authUser->can('Replicate:ExpenseOps');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExpenseOps');
    }

}