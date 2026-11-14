<?php

namespace App\Filament\Resources\Provas\Pages;

use App\Filament\Resources\Provas\ProvaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProva extends EditRecord
{
    protected static string $resource = ProvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
