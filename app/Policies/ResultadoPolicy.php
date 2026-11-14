<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Resultado;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResultadoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Resultado');
    }

    public function view(AuthUser $authUser, Resultado $resultado): bool
    {
        return $authUser->can('View:Resultado');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Resultado');
    }

    public function update(AuthUser $authUser, Resultado $resultado): bool
    {
        return $authUser->can('Update:Resultado');
    }

    public function delete(AuthUser $authUser, Resultado $resultado): bool
    {
        return $authUser->can('Delete:Resultado');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Resultado');
    }

    public function restore(AuthUser $authUser, Resultado $resultado): bool
    {
        return $authUser->can('Restore:Resultado');
    }

    public function forceDelete(AuthUser $authUser, Resultado $resultado): bool
    {
        return $authUser->can('ForceDelete:Resultado');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Resultado');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Resultado');
    }

    public function replicate(AuthUser $authUser, Resultado $resultado): bool
    {
        return $authUser->can('Replicate:Resultado');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Resultado');
    }

}