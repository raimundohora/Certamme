<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LocalProva;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocalProvaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LocalProva');
    }

    public function view(AuthUser $authUser, LocalProva $localProva): bool
    {
        return $authUser->can('View:LocalProva');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LocalProva');
    }

    public function update(AuthUser $authUser, LocalProva $localProva): bool
    {
        return $authUser->can('Update:LocalProva');
    }

    public function delete(AuthUser $authUser, LocalProva $localProva): bool
    {
        return $authUser->can('Delete:LocalProva');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LocalProva');
    }

    public function restore(AuthUser $authUser, LocalProva $localProva): bool
    {
        return $authUser->can('Restore:LocalProva');
    }

    public function forceDelete(AuthUser $authUser, LocalProva $localProva): bool
    {
        return $authUser->can('ForceDelete:LocalProva');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LocalProva');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LocalProva');
    }

    public function replicate(AuthUser $authUser, LocalProva $localProva): bool
    {
        return $authUser->can('Replicate:LocalProva');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LocalProva');
    }

}