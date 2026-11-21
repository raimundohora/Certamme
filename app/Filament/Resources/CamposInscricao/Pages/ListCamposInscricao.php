<?php

namespace App\Filament\Resources\CamposInscricao\Pages;

use App\Filament\Resources\CamposInscricao\CampoInscricaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCamposInscricao extends ListRecords
{
    protected static string $resource = CampoInscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
