<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Industry;
use Illuminate\Auth\Access\HandlesAuthorization;

class IndustryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Industry');
    }

    public function view(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('View:Industry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Industry');
    }

    public function update(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('Update:Industry');
    }

    public function delete(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('Delete:Industry');
    }

    public function restore(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('Restore:Industry');
    }

    public function forceDelete(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('ForceDelete:Industry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Industry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Industry');
    }

    public function replicate(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('Replicate:Industry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Industry');
    }

}