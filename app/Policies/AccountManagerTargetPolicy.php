<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AccountManagerTarget;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountManagerTargetPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AccountManagerTarget');
    }

    public function view(AuthUser $authUser, AccountManagerTarget $accountManagerTarget): bool
    {
        return $authUser->can('View:AccountManagerTarget');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AccountManagerTarget');
    }

    public function update(AuthUser $authUser, AccountManagerTarget $accountManagerTarget): bool
    {
        return $authUser->can('Update:AccountManagerTarget');
    }

    public function delete(AuthUser $authUser, AccountManagerTarget $accountManagerTarget): bool
    {
        return $authUser->can('Delete:AccountManagerTarget');
    }

    public function restore(AuthUser $authUser, AccountManagerTarget $accountManagerTarget): bool
    {
        return $authUser->can('Restore:AccountManagerTarget');
    }

    public function forceDelete(AuthUser $authUser, AccountManagerTarget $accountManagerTarget): bool
    {
        return $authUser->can('ForceDelete:AccountManagerTarget');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AccountManagerTarget');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AccountManagerTarget');
    }

    public function replicate(AuthUser $authUser, AccountManagerTarget $accountManagerTarget): bool
    {
        return $authUser->can('Replicate:AccountManagerTarget');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AccountManagerTarget');
    }

}