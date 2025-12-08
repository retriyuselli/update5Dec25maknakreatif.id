<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Prospect;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProspectPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Prospect');
    }

    public function view(AuthUser $authUser, Prospect $prospect): bool
    {
        return $authUser->can('View:Prospect');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Prospect');
    }

    public function update(AuthUser $authUser, Prospect $prospect): bool
    {
        return $authUser->can('Update:Prospect');
    }

    public function delete(AuthUser $authUser, Prospect $prospect): bool
    {
        return $authUser->can('Delete:Prospect');
    }

    public function restore(AuthUser $authUser, Prospect $prospect): bool
    {
        return $authUser->can('Restore:Prospect');
    }

    public function forceDelete(AuthUser $authUser, Prospect $prospect): bool
    {
        return $authUser->can('ForceDelete:Prospect');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Prospect');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Prospect');
    }

    public function replicate(AuthUser $authUser, Prospect $prospect): bool
    {
        return $authUser->can('Replicate:Prospect');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Prospect');
    }

}