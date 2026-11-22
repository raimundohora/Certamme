<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    /** Trilha imutável: sem ação de criar. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
