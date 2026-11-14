<?php

namespace App\Filament\Resources\ProvaSubjetivas\Pages;

use App\Filament\Resources\ProvaSubjetivas\ProvaSubjetivaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProvaSubjetiva extends EditRecord
{
    protected static string $resource = ProvaSubjetivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
