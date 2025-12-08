<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PendapatanLain;
use Illuminate\Auth\Access\HandlesAuthorization;

class PendapatanLainPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PendapatanLain');
    }

    public function view(AuthUser $authUser, PendapatanLain $pendapatanLain): bool
    {
        return $authUser->can('View:PendapatanLain');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PendapatanLain');
    }

    public function update(AuthUser $authUser, PendapatanLain $pendapatanLain): bool
    {
        return $authUser->can('Update:PendapatanLain');
    }

    public function delete(AuthUser $authUser, PendapatanLain $pendapatanLain): bool
    {
        return $authUser->can('Delete:PendapatanLain');
    }

    public function restore(AuthUser $authUser, PendapatanLain $pendapatanLain): bool
    {
        return $authUser->can('Restore:PendapatanLain');
    }

    public function forceDelete(AuthUser $authUser, PendapatanLain $pendapatanLain): bool
    {
        return $authUser->can('ForceDelete:PendapatanLain');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PendapatanLain');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PendapatanLain');
    }

    public function replicate(AuthUser $authUser, PendapatanLain $pendapatanLain): bool
    {
        return $authUser->can('Replicate:PendapatanLain');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PendapatanLain');
    }

}