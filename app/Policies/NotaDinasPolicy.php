<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NotaDinas;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotaDinasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NotaDinas');
    }

    public function view(AuthUser $authUser, NotaDinas $notaDinas): bool
    {
        return $authUser->can('View:NotaDinas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NotaDinas');
    }

    public function update(AuthUser $authUser, NotaDinas $notaDinas): bool
    {
        return $authUser->can('Update:NotaDinas');
    }

    public function delete(AuthUser $authUser, NotaDinas $notaDinas): bool
    {
        return $authUser->can('Delete:NotaDinas');
    }

    public function restore(AuthUser $authUser, NotaDinas $notaDinas): bool
    {
        return $authUser->can('Restore:NotaDinas');
    }

    public function forceDelete(AuthUser $authUser, NotaDinas $notaDinas): bool
    {
        return $authUser->can('ForceDelete:NotaDinas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NotaDinas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NotaDinas');
    }

    public function replicate(AuthUser $authUser, NotaDinas $notaDinas): bool
    {
        return $authUser->can('Replicate:NotaDinas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NotaDinas');
    }

}