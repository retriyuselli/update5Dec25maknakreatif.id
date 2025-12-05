<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FixedAsset;
use Illuminate\Auth\Access\HandlesAuthorization;

class FixedAssetPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FixedAsset');
    }

    public function view(AuthUser $authUser, FixedAsset $fixedAsset): bool
    {
        return $authUser->can('View:FixedAsset');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FixedAsset');
    }

    public function update(AuthUser $authUser, FixedAsset $fixedAsset): bool
    {
        return $authUser->can('Update:FixedAsset');
    }

    public function delete(AuthUser $authUser, FixedAsset $fixedAsset): bool
    {
        return $authUser->can('Delete:FixedAsset');
    }

    public function restore(AuthUser $authUser, FixedAsset $fixedAsset): bool
    {
        return $authUser->can('Restore:FixedAsset');
    }

    public function forceDelete(AuthUser $authUser, FixedAsset $fixedAsset): bool
    {
        return $authUser->can('ForceDelete:FixedAsset');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FixedAsset');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FixedAsset');
    }

    public function replicate(AuthUser $authUser, FixedAsset $fixedAsset): bool
    {
        return $authUser->can('Replicate:FixedAsset');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FixedAsset');
    }

}