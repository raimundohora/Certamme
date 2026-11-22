<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /** Novo colaborador criado no admin nasce vinculado à organizadora atual. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['tenant_id']) && ($tenant = Tenant::current())) {
            $data['tenant_id'] = $tenant->id;
        }

        return $data;
    }
}
