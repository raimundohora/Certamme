<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LeituraCartao;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeituraCartaoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeituraCartao');
    }

    public function view(AuthUser $authUser, LeituraCartao $leituraCartao): bool
    {
        return $authUser->can('View:LeituraCartao');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeituraCartao');
    }

    public function update(AuthUser $authUser, LeituraCartao $leituraCartao): bool
    {
        return $authUser->can('Update:LeituraCartao');
    }

    public function delete(AuthUser $authUser, LeituraCartao $leituraCartao): bool
    {
        return $authUser->can('Delete:LeituraCartao');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeituraCartao');
    }

    public function restore(AuthUser $authUser, LeituraCartao $leituraCartao): bool
    {
        return $authUser->can('Restore:LeituraCartao');
    }

    public function forceDelete(AuthUser $authUser, LeituraCartao $leituraCartao): bool
    {
        return $authUser->can('ForceDelete:LeituraCartao');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeituraCartao');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeituraCartao');
    }

    public function replicate(AuthUser $authUser, LeituraCartao $leituraCartao): bool
    {
        return $authUser->can('Replicate:LeituraCartao');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeituraCartao');
    }

}