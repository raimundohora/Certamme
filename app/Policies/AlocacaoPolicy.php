<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Alocacao;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlocacaoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Alocacao');
    }

    public function view(AuthUser $authUser, Alocacao $alocacao): bool
    {
        return $authUser->can('View:Alocacao');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Alocacao');
    }

    public function update(AuthUser $authUser, Alocacao $alocacao): bool
    {
        return $authUser->can('Update:Alocacao');
    }

    public function delete(AuthUser $authUser, Alocacao $alocacao): bool
    {
        return $authUser->can('Delete:Alocacao');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Alocacao');
    }

    public function restore(AuthUser $authUser, Alocacao $alocacao): bool
    {
        return $authUser->can('Restore:Alocacao');
    }

    public function forceDelete(AuthUser $authUser, Alocacao $alocacao): bool
    {
        return $authUser->can('ForceDelete:Alocacao');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Alocacao');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Alocacao');
    }

    public function replicate(AuthUser $authUser, Alocacao $alocacao): bool
    {
        return $authUser->can('Replicate:Alocacao');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Alocacao');
    }

}