<?php

namespace App\Filament\Resources\CorrecaoSubjetivas\Pages;

use App\Filament\Resources\CorrecaoSubjetivas\CorrecaoSubjetivaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCorrecaoSubjetiva extends EditRecord
{
    protected static string $resource = CorrecaoSubjetivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
