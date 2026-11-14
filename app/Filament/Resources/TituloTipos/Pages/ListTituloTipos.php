<?php

namespace App\Filament\Resources\TituloTipos\Pages;

use App\Filament\Resources\TituloTipos\TituloTipoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTituloTipos extends ListRecords
{
    protected static string $resource = TituloTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
