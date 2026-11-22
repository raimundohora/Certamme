<?php

namespace App\Filament\Resources\CorrecaoSubjetivas\Pages;

use App\Filament\Resources\CorrecaoSubjetivas\CorrecaoSubjetivaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCorrecaoSubjetivas extends ListRecords
{
    protected static string $resource = CorrecaoSubjetivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nova correção'),
        ];
    }
}
