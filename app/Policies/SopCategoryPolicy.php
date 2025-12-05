<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SopCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class SopCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SopCategory');
    }

    public function view(AuthUser $authUser, SopCategory $sopCategory): bool
    {
        return $authUser->can('View:SopCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SopCategory');
    }

    public function update(AuthUser $authUser, SopCategory $sopCategory): bool
    {
        return $authUser->can('Update:SopCategory');
    }

    public function delete(AuthUser $authUser, SopCategory $sopCategory): bool
    {
        return $authUser->can('Delete:SopCategory');
    }

    public function restore(AuthUser $authUser, SopCategory $sopCategory): bool
    {
        return $authUser->can('Restore:SopCategory');
    }

    public function forceDelete(AuthUser $authUser, SopCategory $sopCategory): bool
    {
        return $authUser->can('ForceDelete:SopCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SopCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SopCategory');
    }

    public function replicate(AuthUser $authUser, SopCategory $sopCategory): bool
    {
        return $authUser->can('Replicate:SopCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SopCategory');
    }

}