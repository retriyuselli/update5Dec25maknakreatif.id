<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ChartOfAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChartOfAccountPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ChartOfAccount');
    }

    public function view(AuthUser $authUser, ChartOfAccount $chartOfAccount): bool
    {
        return $authUser->can('View:ChartOfAccount');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ChartOfAccount');
    }

    public function update(AuthUser $authUser, ChartOfAccount $chartOfAccount): bool
    {
        return $authUser->can('Update:ChartOfAccount');
    }

    public function delete(AuthUser $authUser, ChartOfAccount $chartOfAccount): bool
    {
        return $authUser->can('Delete:ChartOfAccount');
    }

    public function restore(AuthUser $authUser, ChartOfAccount $chartOfAccount): bool
    {
        return $authUser->can('Restore:ChartOfAccount');
    }

    public function forceDelete(AuthUser $authUser, ChartOfAccount $chartOfAccount): bool
    {
        return $authUser->can('ForceDelete:ChartOfAccount');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ChartOfAccount');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ChartOfAccount');
    }

    public function replicate(AuthUser $authUser, ChartOfAccount $chartOfAccount): bool
    {
        return $authUser->can('Replicate:ChartOfAccount');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ChartOfAccount');
    }

}