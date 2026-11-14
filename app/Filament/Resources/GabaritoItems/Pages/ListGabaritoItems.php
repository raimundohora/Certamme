<?php

namespace App\Filament\Resources\GabaritoItems\Pages;

use App\Filament\Resources\GabaritoItems\GabaritoItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGabaritoItems extends ListRecords
{
    protected static string $resource = GabaritoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
