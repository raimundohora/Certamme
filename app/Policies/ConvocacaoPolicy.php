<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Convocacao;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConvocacaoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Convocacao');
    }

    public function view(AuthUser $authUser, Convocacao $convocacao): bool
    {
        return $authUser->can('View:Convocacao');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Convocacao');
    }

    public function update(AuthUser $authUser, Convocacao $convocacao): bool
    {
        return $authUser->can('Update:Convocacao');
    }

    public function delete(AuthUser $authUser, Convocacao $convocacao): bool
    {
        return $authUser->can('Delete:Convocacao');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Convocacao');
    }

    public function restore(AuthUser $authUser, Convocacao $convocacao): bool
    {
        return $authUser->can('Restore:Convocacao');
    }

    public function forceDelete(AuthUser $authUser, Convocacao $convocacao): bool
    {
        return $authUser->can('ForceDelete:Convocacao');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Convocacao');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Convocacao');
    }

    public function replicate(AuthUser $authUser, Convocacao $convocacao): bool
    {
        return $authUser->can('Replicate:Convocacao');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Convocacao');
    }

}