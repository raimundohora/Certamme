<?php

namespace App\Filament\Resources\LocalProvas\Pages;

use App\Filament\Resources\LocalProvas\LocalProvaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLocalProvas extends ListRecords
{
    protected static string $resource = LocalProvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
