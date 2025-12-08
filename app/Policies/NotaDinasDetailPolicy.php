<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NotaDinasDetail;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotaDinasDetailPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NotaDinasDetail');
    }

    public function view(AuthUser $authUser, NotaDinasDetail $notaDinasDetail): bool
    {
        return $authUser->can('View:NotaDinasDetail');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NotaDinasDetail');
    }

    public function update(AuthUser $authUser, NotaDinasDetail $notaDinasDetail): bool
    {
        return $authUser->can('Update:NotaDinasDetail');
    }

    public function delete(AuthUser $authUser, NotaDinasDetail $notaDinasDetail): bool
    {
        return $authUser->can('Delete:NotaDinasDetail');
    }

    public function restore(AuthUser $authUser, NotaDinasDetail $notaDinasDetail): bool
    {
        return $authUser->can('Restore:NotaDinasDetail');
    }

    public function forceDelete(AuthUser $authUser, NotaDinasDetail $notaDinasDetail): bool
    {
        return $authUser->can('ForceDelete:NotaDinasDetail');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NotaDinasDetail');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NotaDinasDetail');
    }

    public function replicate(AuthUser $authUser, NotaDinasDetail $notaDinasDetail): bool
    {
        return $authUser->can('Replicate:NotaDinasDetail');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NotaDinasDetail');
    }

}