<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LeaveBalance;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaveBalancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeaveBalance');
    }

    public function view(AuthUser $authUser, LeaveBalance $leaveBalance): bool
    {
        return $authUser->can('View:LeaveBalance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeaveBalance');
    }

    public function update(AuthUser $authUser, LeaveBalance $leaveBalance): bool
    {
        return $authUser->can('Update:LeaveBalance');
    }

    public function delete(AuthUser $authUser, LeaveBalance $leaveBalance): bool
    {
        return $authUser->can('Delete:LeaveBalance');
    }

    public function restore(AuthUser $authUser, LeaveBalance $leaveBalance): bool
    {
        return $authUser->can('Restore:LeaveBalance');
    }

    public function forceDelete(AuthUser $authUser, LeaveBalance $leaveBalance): bool
    {
        return $authUser->can('ForceDelete:LeaveBalance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeaveBalance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeaveBalance');
    }

    public function replicate(AuthUser $authUser, LeaveBalance $leaveBalance): bool
    {
        return $authUser->can('Replicate:LeaveBalance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeaveBalance');
    }

}