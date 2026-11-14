<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Prova;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Prova');
    }

    public function view(AuthUser $authUser, Prova $prova): bool
    {
        return $authUser->can('View:Prova');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Prova');
    }

    public function update(AuthUser $authUser, Prova $prova): bool
    {
        return $authUser->can('Update:Prova');
    }

    public function delete(AuthUser $authUser, Prova $prova): bool
    {
        return $authUser->can('Delete:Prova');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Prova');
    }

    public function restore(AuthUser $authUser, Prova $prova): bool
    {
        return $authUser->can('Restore:Prova');
    }

    public function forceDelete(AuthUser $authUser, Prova $prova): bool
    {
        return $authUser->can('ForceDelete:Prova');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Prova');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Prova');
    }

    public function replicate(AuthUser $authUser, Prova $prova): bool
    {
        return $authUser->can('Replicate:Prova');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Prova');
    }

}