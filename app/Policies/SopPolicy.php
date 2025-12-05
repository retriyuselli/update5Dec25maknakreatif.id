<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Sop;
use Illuminate\Auth\Access\HandlesAuthorization;

class SopPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Sop');
    }

    public function view(AuthUser $authUser, Sop $sop): bool
    {
        return $authUser->can('View:Sop');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Sop');
    }

    public function update(AuthUser $authUser, Sop $sop): bool
    {
        return $authUser->can('Update:Sop');
    }

    public function delete(AuthUser $authUser, Sop $sop): bool
    {
        return $authUser->can('Delete:Sop');
    }

    public function restore(AuthUser $authUser, Sop $sop): bool
    {
        return $authUser->can('Restore:Sop');
    }

    public function forceDelete(AuthUser $authUser, Sop $sop): bool
    {
        return $authUser->can('ForceDelete:Sop');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Sop');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Sop');
    }

    public function replicate(AuthUser $authUser, Sop $sop): bool
    {
        return $authUser->can('Replicate:Sop');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Sop');
    }

}