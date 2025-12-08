<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SimulasiProduk;
use Illuminate\Auth\Access\HandlesAuthorization;

class SimulasiProdukPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SimulasiProduk');
    }

    public function view(AuthUser $authUser, SimulasiProduk $simulasiProduk): bool
    {
        return $authUser->can('View:SimulasiProduk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SimulasiProduk');
    }

    public function update(AuthUser $authUser, SimulasiProduk $simulasiProduk): bool
    {
        return $authUser->can('Update:SimulasiProduk');
    }

    public function delete(AuthUser $authUser, SimulasiProduk $simulasiProduk): bool
    {
        return $authUser->can('Delete:SimulasiProduk');
    }

    public function restore(AuthUser $authUser, SimulasiProduk $simulasiProduk): bool
    {
        return $authUser->can('Restore:SimulasiProduk');
    }

    public function forceDelete(AuthUser $authUser, SimulasiProduk $simulasiProduk): bool
    {
        return $authUser->can('ForceDelete:SimulasiProduk');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SimulasiProduk');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SimulasiProduk');
    }

    public function replicate(AuthUser $authUser, SimulasiProduk $simulasiProduk): bool
    {
        return $authUser->can('Replicate:SimulasiProduk');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SimulasiProduk');
    }

}