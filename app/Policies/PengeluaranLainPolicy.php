<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PengeluaranLain;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengeluaranLainPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PengeluaranLain');
    }

    public function view(AuthUser $authUser, PengeluaranLain $pengeluaranLain): bool
    {
        return $authUser->can('View:PengeluaranLain');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PengeluaranLain');
    }

    public function update(AuthUser $authUser, PengeluaranLain $pengeluaranLain): bool
    {
        return $authUser->can('Update:PengeluaranLain');
    }

    public function delete(AuthUser $authUser, PengeluaranLain $pengeluaranLain): bool
    {
        return $authUser->can('Delete:PengeluaranLain');
    }

    public function restore(AuthUser $authUser, PengeluaranLain $pengeluaranLain): bool
    {
        return $authUser->can('Restore:PengeluaranLain');
    }

    public function forceDelete(AuthUser $authUser, PengeluaranLain $pengeluaranLain): bool
    {
        return $authUser->can('ForceDelete:PengeluaranLain');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PengeluaranLain');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PengeluaranLain');
    }

    public function replicate(AuthUser $authUser, PengeluaranLain $pengeluaranLain): bool
    {
        return $authUser->can('Replicate:PengeluaranLain');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PengeluaranLain');
    }

}