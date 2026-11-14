<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Inscricao;
use Illuminate\Auth\Access\HandlesAuthorization;

class InscricaoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Inscricao');
    }

    public function view(AuthUser $authUser, Inscricao $inscricao): bool
    {
        return $authUser->can('View:Inscricao');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Inscricao');
    }

    public function update(AuthUser $authUser, Inscricao $inscricao): bool
    {
        return $authUser->can('Update:Inscricao');
    }

    public function delete(AuthUser $authUser, Inscricao $inscricao): bool
    {
        return $authUser->can('Delete:Inscricao');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Inscricao');
    }

    public function restore(AuthUser $authUser, Inscricao $inscricao): bool
    {
        return $authUser->can('Restore:Inscricao');
    }

    public function forceDelete(AuthUser $authUser, Inscricao $inscricao): bool
    {
        return $authUser->can('ForceDelete:Inscricao');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Inscricao');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Inscricao');
    }

    public function replicate(AuthUser $authUser, Inscricao $inscricao): bool
    {
        return $authUser->can('Replicate:Inscricao');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Inscricao');
    }

}