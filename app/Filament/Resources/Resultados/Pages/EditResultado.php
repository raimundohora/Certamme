<?php

namespace App\Filament\Resources\Resultados\Pages;

use App\Filament\Resources\Resultados\ResultadoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResultado extends EditRecord
{
    protected static string $resource = ResultadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
