<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Isencao;
use Illuminate\Auth\Access\HandlesAuthorization;

class IsencaoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Isencao');
    }

    public function view(AuthUser $authUser, Isencao $isencao): bool
    {
        return $authUser->can('View:Isencao');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Isencao');
    }

    public function update(AuthUser $authUser, Isencao $isencao): bool
    {
        return $authUser->can('Update:Isencao');
    }

    public function delete(AuthUser $authUser, Isencao $isencao): bool
    {
        return $authUser->can('Delete:Isencao');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Isencao');
    }

    public function restore(AuthUser $authUser, Isencao $isencao): bool
    {
        return $authUser->can('Restore:Isencao');
    }

    public function forceDelete(AuthUser $authUser, Isencao $isencao): bool
    {
        return $authUser->can('ForceDelete:Isencao');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Isencao');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Isencao');
    }

    public function replicate(AuthUser $authUser, Isencao $isencao): bool
    {
        return $authUser->can('Replicate:Isencao');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Isencao');
    }

}