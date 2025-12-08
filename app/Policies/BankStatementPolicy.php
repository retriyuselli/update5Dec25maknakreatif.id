<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BankStatement;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankStatementPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BankStatement');
    }

    public function view(AuthUser $authUser, BankStatement $bankStatement): bool
    {
        return $authUser->can('View:BankStatement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BankStatement');
    }

    public function update(AuthUser $authUser, BankStatement $bankStatement): bool
    {
        return $authUser->can('Update:BankStatement');
    }

    public function delete(AuthUser $authUser, BankStatement $bankStatement): bool
    {
        return $authUser->can('Delete:BankStatement');
    }

    public function restore(AuthUser $authUser, BankStatement $bankStatement): bool
    {
        return $authUser->can('Restore:BankStatement');
    }

    public function forceDelete(AuthUser $authUser, BankStatement $bankStatement): bool
    {
        return $authUser->can('ForceDelete:BankStatement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BankStatement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BankStatement');
    }

    public function replicate(AuthUser $authUser, BankStatement $bankStatement): bool
    {
        return $authUser->can('Replicate:BankStatement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BankStatement');
    }

}