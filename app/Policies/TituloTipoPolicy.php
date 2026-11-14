<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TituloTipo;
use Illuminate\Auth\Access\HandlesAuthorization;

class TituloTipoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TituloTipo');
    }

    public function view(AuthUser $authUser, TituloTipo $tituloTipo): bool
    {
        return $authUser->can('View:TituloTipo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TituloTipo');
    }

    public function update(AuthUser $authUser, TituloTipo $tituloTipo): bool
    {
        return $authUser->can('Update:TituloTipo');
    }

    public function delete(AuthUser $authUser, TituloTipo $tituloTipo): bool
    {
        return $authUser->can('Delete:TituloTipo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TituloTipo');
    }

    public function restore(AuthUser $authUser, TituloTipo $tituloTipo): bool
    {
        return $authUser->can('Restore:TituloTipo');
    }

    public function forceDelete(AuthUser $authUser, TituloTipo $tituloTipo): bool
    {
        return $authUser->can('ForceDelete:TituloTipo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TituloTipo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TituloTipo');
    }

    public function replicate(AuthUser $authUser, TituloTipo $tituloTipo): bool
    {
        return $authUser->can('Replicate:TituloTipo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TituloTipo');
    }

}