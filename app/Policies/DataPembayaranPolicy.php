<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DataPembayaran;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataPembayaranPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DataPembayaran');
    }

    public function view(AuthUser $authUser, DataPembayaran $dataPembayaran): bool
    {
        return $authUser->can('View:DataPembayaran');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DataPembayaran');
    }

    public function update(AuthUser $authUser, DataPembayaran $dataPembayaran): bool
    {
        return $authUser->can('Update:DataPembayaran');
    }

    public function delete(AuthUser $authUser, DataPembayaran $dataPembayaran): bool
    {
        return $authUser->can('Delete:DataPembayaran');
    }

    public function restore(AuthUser $authUser, DataPembayaran $dataPembayaran): bool
    {
        return $authUser->can('Restore:DataPembayaran');
    }

    public function forceDelete(AuthUser $authUser, DataPembayaran $dataPembayaran): bool
    {
        return $authUser->can('ForceDelete:DataPembayaran');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DataPembayaran');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DataPembayaran');
    }

    public function replicate(AuthUser $authUser, DataPembayaran $dataPembayaran): bool
    {
        return $authUser->can('Replicate:DataPembayaran');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DataPembayaran');
    }

}