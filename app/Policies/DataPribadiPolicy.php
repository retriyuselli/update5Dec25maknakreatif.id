<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DataPribadi;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataPribadiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DataPribadi');
    }

    public function view(AuthUser $authUser, DataPribadi $dataPribadi): bool
    {
        return $authUser->can('View:DataPribadi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DataPribadi');
    }

    public function update(AuthUser $authUser, DataPribadi $dataPribadi): bool
    {
        return $authUser->can('Update:DataPribadi');
    }

    public function delete(AuthUser $authUser, DataPribadi $dataPribadi): bool
    {
        return $authUser->can('Delete:DataPribadi');
    }

    public function restore(AuthUser $authUser, DataPribadi $dataPribadi): bool
    {
        return $authUser->can('Restore:DataPribadi');
    }

    public function forceDelete(AuthUser $authUser, DataPribadi $dataPribadi): bool
    {
        return $authUser->can('ForceDelete:DataPribadi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DataPribadi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DataPribadi');
    }

    public function replicate(AuthUser $authUser, DataPribadi $dataPribadi): bool
    {
        return $authUser->can('Replicate:DataPribadi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DataPribadi');
    }

}