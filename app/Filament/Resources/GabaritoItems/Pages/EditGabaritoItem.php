<?php

namespace App\Filament\Resources\GabaritoItems\Pages;

use App\Filament\Resources\GabaritoItems\GabaritoItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGabaritoItem extends EditRecord
{
    protected static string $resource = GabaritoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
