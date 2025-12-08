<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CompanyLogo;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyLogoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CompanyLogo');
    }

    public function view(AuthUser $authUser, CompanyLogo $companyLogo): bool
    {
        return $authUser->can('View:CompanyLogo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CompanyLogo');
    }

    public function update(AuthUser $authUser, CompanyLogo $companyLogo): bool
    {
        return $authUser->can('Update:CompanyLogo');
    }

    public function delete(AuthUser $authUser, CompanyLogo $companyLogo): bool
    {
        return $authUser->can('Delete:CompanyLogo');
    }

    public function restore(AuthUser $authUser, CompanyLogo $companyLogo): bool
    {
        return $authUser->can('Restore:CompanyLogo');
    }

    public function forceDelete(AuthUser $authUser, CompanyLogo $companyLogo): bool
    {
        return $authUser->can('ForceDelete:CompanyLogo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CompanyLogo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CompanyLogo');
    }

    public function replicate(AuthUser $authUser, CompanyLogo $companyLogo): bool
    {
        return $authUser->can('Replicate:CompanyLogo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CompanyLogo');
    }

}