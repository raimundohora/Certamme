<?php

namespace App\Filament\Resources\Alocacaos\Pages;

use App\Filament\Resources\Alocacaos\AlocacaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlocacaos extends ListRecords
{
    protected static string $resource = AlocacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
