<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PembayaranPiutang;
use Illuminate\Auth\Access\HandlesAuthorization;

class PembayaranPiutangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PembayaranPiutang');
    }

    public function view(AuthUser $authUser, PembayaranPiutang $pembayaranPiutang): bool
    {
        return $authUser->can('View:PembayaranPiutang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PembayaranPiutang');
    }

    public function update(AuthUser $authUser, PembayaranPiutang $pembayaranPiutang): bool
    {
        return $authUser->can('Update:PembayaranPiutang');
    }

    public function delete(AuthUser $authUser, PembayaranPiutang $pembayaranPiutang): bool
    {
        return $authUser->can('Delete:PembayaranPiutang');
    }

    public function restore(AuthUser $authUser, PembayaranPiutang $pembayaranPiutang): bool
    {
        return $authUser->can('Restore:PembayaranPiutang');
    }

    public function forceDelete(AuthUser $authUser, PembayaranPiutang $pembayaranPiutang): bool
    {
        return $authUser->can('ForceDelete:PembayaranPiutang');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PembayaranPiutang');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PembayaranPiutang');
    }

    public function replicate(AuthUser $authUser, PembayaranPiutang $pembayaranPiutang): bool
    {
        return $authUser->can('Replicate:PembayaranPiutang');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PembayaranPiutang');
    }

}