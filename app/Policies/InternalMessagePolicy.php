<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InternalMessage;
use Illuminate\Auth\Access\HandlesAuthorization;

class InternalMessagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InternalMessage');
    }

    public function view(AuthUser $authUser, InternalMessage $internalMessage): bool
    {
        return $authUser->can('View:InternalMessage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InternalMessage');
    }

    public function update(AuthUser $authUser, InternalMessage $internalMessage): bool
    {
        return $authUser->can('Update:InternalMessage');
    }

    public function delete(AuthUser $authUser, InternalMessage $internalMessage): bool
    {
        return $authUser->can('Delete:InternalMessage');
    }

    public function restore(AuthUser $authUser, InternalMessage $internalMessage): bool
    {
        return $authUser->can('Restore:InternalMessage');
    }

    public function forceDelete(AuthUser $authUser, InternalMessage $internalMessage): bool
    {
        return $authUser->can('ForceDelete:InternalMessage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InternalMessage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InternalMessage');
    }

    public function replicate(AuthUser $authUser, InternalMessage $internalMessage): bool
    {
        return $authUser->can('Replicate:InternalMessage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InternalMessage');
    }

}