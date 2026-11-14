<?php

namespace App\Filament\Resources\Concursos\Pages;

use App\Filament\Resources\Concursos\ConcursoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConcursos extends ListRecords
{
    protected static string $resource = ConcursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
