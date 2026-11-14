<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Pagamento;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagamentoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Pagamento');
    }

    public function view(AuthUser $authUser, Pagamento $pagamento): bool
    {
        return $authUser->can('View:Pagamento');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Pagamento');
    }

    public function update(AuthUser $authUser, Pagamento $pagamento): bool
    {
        return $authUser->can('Update:Pagamento');
    }

    public function delete(AuthUser $authUser, Pagamento $pagamento): bool
    {
        return $authUser->can('Delete:Pagamento');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Pagamento');
    }

    public function restore(AuthUser $authUser, Pagamento $pagamento): bool
    {
        return $authUser->can('Restore:Pagamento');
    }

    public function forceDelete(AuthUser $authUser, Pagamento $pagamento): bool
    {
        return $authUser->can('ForceDelete:Pagamento');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Pagamento');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Pagamento');
    }

    public function replicate(AuthUser $authUser, Pagamento $pagamento): bool
    {
        return $authUser->can('Replicate:Pagamento');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Pagamento');
    }

}