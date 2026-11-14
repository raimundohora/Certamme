<?php

namespace App\Filament\Resources\Isencaos\Pages;

use App\Filament\Resources\Isencaos\IsencaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIsencao extends EditRecord
{
    protected static string $resource = IsencaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
