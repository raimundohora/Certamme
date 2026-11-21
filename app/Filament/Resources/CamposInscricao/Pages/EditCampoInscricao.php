<?php

namespace App\Filament\Resources\CamposInscricao\Pages;

use App\Filament\Resources\CamposInscricao\CampoInscricaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampoInscricao extends EditRecord
{
    protected static string $resource = CampoInscricaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
