<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Piutang;
use Illuminate\Auth\Access\HandlesAuthorization;

class PiutangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Piutang');
    }

    public function view(AuthUser $authUser, Piutang $piutang): bool
    {
        return $authUser->can('View:Piutang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Piutang');
    }

    public function update(AuthUser $authUser, Piutang $piutang): bool
    {
        return $authUser->can('Update:Piutang');
    }

    public function delete(AuthUser $authUser, Piutang $piutang): bool
    {
        return $authUser->can('Delete:Piutang');
    }

    public function restore(AuthUser $authUser, Piutang $piutang): bool
    {
        return $authUser->can('Restore:Piutang');
    }

    public function forceDelete(AuthUser $authUser, Piutang $piutang): bool
    {
        return $authUser->can('ForceDelete:Piutang');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Piutang');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Piutang');
    }

    public function replicate(AuthUser $authUser, Piutang $piutang): bool
    {
        return $authUser->can('Replicate:Piutang');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Piutang');
    }

}