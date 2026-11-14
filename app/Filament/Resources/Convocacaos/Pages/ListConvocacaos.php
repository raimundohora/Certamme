<?php

namespace App\Filament\Resources\Convocacaos\Pages;

use App\Filament\Resources\Convocacaos\ConvocacaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConvocacaos extends ListRecords
{
    protected static string $resource = ConvocacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
