<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Recurso;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecursoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Recurso');
    }

    public function view(AuthUser $authUser, Recurso $recurso): bool
    {
        return $authUser->can('View:Recurso');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Recurso');
    }

    public function update(AuthUser $authUser, Recurso $recurso): bool
    {
        return $authUser->can('Update:Recurso');
    }

    public function delete(AuthUser $authUser, Recurso $recurso): bool
    {
        return $authUser->can('Delete:Recurso');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Recurso');
    }

    public function restore(AuthUser $authUser, Recurso $recurso): bool
    {
        return $authUser->can('Restore:Recurso');
    }

    public function forceDelete(AuthUser $authUser, Recurso $recurso): bool
    {
        return $authUser->can('ForceDelete:Recurso');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Recurso');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Recurso');
    }

    public function replicate(AuthUser $authUser, Recurso $recurso): bool
    {
        return $authUser->can('Replicate:Recurso');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Recurso');
    }

}