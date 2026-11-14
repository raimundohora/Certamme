<?php

namespace App\Filament\Resources\TituloTipos\Pages;

use App\Filament\Resources\TituloTipos\TituloTipoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTituloTipo extends EditRecord
{
    protected static string $resource = TituloTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
