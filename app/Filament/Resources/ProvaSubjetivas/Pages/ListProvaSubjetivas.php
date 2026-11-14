<?php

namespace App\Filament\Resources\ProvaSubjetivas\Pages;

use App\Filament\Resources\ProvaSubjetivas\ProvaSubjetivaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProvaSubjetivas extends ListRecords
{
    protected static string $resource = ProvaSubjetivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
