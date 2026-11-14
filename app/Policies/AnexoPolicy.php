<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Anexo;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnexoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Anexo');
    }

    public function view(AuthUser $authUser, Anexo $anexo): bool
    {
        return $authUser->can('View:Anexo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Anexo');
    }

    public function update(AuthUser $authUser, Anexo $anexo): bool
    {
        return $authUser->can('Update:Anexo');
    }

    public function delete(AuthUser $authUser, Anexo $anexo): bool
    {
        return $authUser->can('Delete:Anexo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Anexo');
    }

    public function restore(AuthUser $authUser, Anexo $anexo): bool
    {
        return $authUser->can('Restore:Anexo');
    }

    public function forceDelete(AuthUser $authUser, Anexo $anexo): bool
    {
        return $authUser->can('ForceDelete:Anexo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Anexo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Anexo');
    }

    public function replicate(AuthUser $authUser, Anexo $anexo): bool
    {
        return $authUser->can('Replicate:Anexo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Anexo');
    }

}