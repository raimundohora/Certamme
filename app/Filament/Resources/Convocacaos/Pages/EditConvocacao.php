<?php

namespace App\Filament\Resources\Convocacaos\Pages;

use App\Filament\Resources\Convocacaos\ConvocacaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConvocacao extends EditRecord
{
    protected static string $resource = ConvocacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
