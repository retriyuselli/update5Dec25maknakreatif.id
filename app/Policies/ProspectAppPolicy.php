<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProspectApp;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProspectAppPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProspectApp');
    }

    public function view(AuthUser $authUser, ProspectApp $prospectApp): bool
    {
        return $authUser->can('View:ProspectApp');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProspectApp');
    }

    public function update(AuthUser $authUser, ProspectApp $prospectApp): bool
    {
        return $authUser->can('Update:ProspectApp');
    }

    public function delete(AuthUser $authUser, ProspectApp $prospectApp): bool
    {
        return $authUser->can('Delete:ProspectApp');
    }

    public function restore(AuthUser $authUser, ProspectApp $prospectApp): bool
    {
        return $authUser->can('Restore:ProspectApp');
    }

    public function forceDelete(AuthUser $authUser, ProspectApp $prospectApp): bool
    {
        return $authUser->can('ForceDelete:ProspectApp');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProspectApp');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProspectApp');
    }

    public function replicate(AuthUser $authUser, ProspectApp $prospectApp): bool
    {
        return $authUser->can('Replicate:ProspectApp');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProspectApp');
    }

}