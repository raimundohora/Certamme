<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProvaSubjetiva;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvaSubjetivaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProvaSubjetiva');
    }

    public function view(AuthUser $authUser, ProvaSubjetiva $provaSubjetiva): bool
    {
        return $authUser->can('View:ProvaSubjetiva');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProvaSubjetiva');
    }

    public function update(AuthUser $authUser, ProvaSubjetiva $provaSubjetiva): bool
    {
        return $authUser->can('Update:ProvaSubjetiva');
    }

    public function delete(AuthUser $authUser, ProvaSubjetiva $provaSubjetiva): bool
    {
        return $authUser->can('Delete:ProvaSubjetiva');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProvaSubjetiva');
    }

    public function restore(AuthUser $authUser, ProvaSubjetiva $provaSubjetiva): bool
    {
        return $authUser->can('Restore:ProvaSubjetiva');
    }

    public function forceDelete(AuthUser $authUser, ProvaSubjetiva $provaSubjetiva): bool
    {
        return $authUser->can('ForceDelete:ProvaSubjetiva');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProvaSubjetiva');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProvaSubjetiva');
    }

    public function replicate(AuthUser $authUser, ProvaSubjetiva $provaSubjetiva): bool
    {
        return $authUser->can('Replicate:ProvaSubjetiva');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProvaSubjetiva');
    }

}