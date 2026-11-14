<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GabaritoItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class GabaritoItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GabaritoItem');
    }

    public function view(AuthUser $authUser, GabaritoItem $gabaritoItem): bool
    {
        return $authUser->can('View:GabaritoItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GabaritoItem');
    }

    public function update(AuthUser $authUser, GabaritoItem $gabaritoItem): bool
    {
        return $authUser->can('Update:GabaritoItem');
    }

    public function delete(AuthUser $authUser, GabaritoItem $gabaritoItem): bool
    {
        return $authUser->can('Delete:GabaritoItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GabaritoItem');
    }

    public function restore(AuthUser $authUser, GabaritoItem $gabaritoItem): bool
    {
        return $authUser->can('Restore:GabaritoItem');
    }

    public function forceDelete(AuthUser $authUser, GabaritoItem $gabaritoItem): bool
    {
        return $authUser->can('ForceDelete:GabaritoItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GabaritoItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GabaritoItem');
    }

    public function replicate(AuthUser $authUser, GabaritoItem $gabaritoItem): bool
    {
        return $authUser->can('Replicate:GabaritoItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GabaritoItem');
    }

}