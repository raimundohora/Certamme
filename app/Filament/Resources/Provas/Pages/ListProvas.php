<?php

namespace App\Filament\Resources\Provas\Pages;

use App\Filament\Resources\Provas\ProvaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProvas extends ListRecords
{
    protected static string $resource = ProvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
