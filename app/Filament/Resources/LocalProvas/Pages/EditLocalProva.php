<?php

namespace App\Filament\Resources\LocalProvas\Pages;

use App\Filament\Resources\LocalProvas\LocalProvaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocalProva extends EditRecord
{
    protected static string $resource = LocalProvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
