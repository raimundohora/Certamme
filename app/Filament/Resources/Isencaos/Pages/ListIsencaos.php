<?php

namespace App\Filament\Resources\Isencaos\Pages;

use App\Filament\Resources\Isencaos\IsencaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIsencaos extends ListRecords
{
    protected static string $resource = IsencaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
