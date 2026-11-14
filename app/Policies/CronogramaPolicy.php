<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Cronograma;
use Illuminate\Auth\Access\HandlesAuthorization;

class CronogramaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cronograma');
    }

    public function view(AuthUser $authUser, Cronograma $cronograma): bool
    {
        return $authUser->can('View:Cronograma');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cronograma');
    }

    public function update(AuthUser $authUser, Cronograma $cronograma): bool
    {
        return $authUser->can('Update:Cronograma');
    }

    public function delete(AuthUser $authUser, Cronograma $cronograma): bool
    {
        return $authUser->can('Delete:Cronograma');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Cronograma');
    }

    public function restore(AuthUser $authUser, Cronograma $cronograma): bool
    {
        return $authUser->can('Restore:Cronograma');
    }

    public function forceDelete(AuthUser $authUser, Cronograma $cronograma): bool
    {
        return $authUser->can('ForceDelete:Cronograma');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Cronograma');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Cronograma');
    }

    public function replicate(AuthUser $authUser, Cronograma $cronograma): bool
    {
        return $authUser->can('Replicate:Cronograma');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Cronograma');
    }

}