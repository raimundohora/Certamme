<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class CandidatoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Candidato');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Candidato');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Candidato');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Candidato');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Candidato');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Candidato');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Candidato');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Candidato');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Candidato');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Candidato');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Candidato');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Candidato');
    }

}