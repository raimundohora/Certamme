<?php

namespace App\Filament\Resources\LeituraCartaos\Pages;

use App\Filament\Resources\LeituraCartaos\LeituraCartaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeituraCartaos extends ListRecords
{
    protected static string $resource = LeituraCartaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
