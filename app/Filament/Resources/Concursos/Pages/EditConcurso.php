<?php

namespace App\Filament\Resources\Concursos\Pages;

use App\Filament\Resources\Concursos\ConcursoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConcurso extends EditRecord
{
    protected static string $resource = ConcursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
