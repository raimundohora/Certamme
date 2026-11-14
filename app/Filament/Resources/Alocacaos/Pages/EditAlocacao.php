<?php

namespace App\Filament\Resources\Alocacaos\Pages;

use App\Filament\Resources\Alocacaos\AlocacaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAlocacao extends EditRecord
{
    protected static string $resource = AlocacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
