<?php

namespace App\Filament\Resources\LeituraCartaos\Pages;

use App\Filament\Resources\LeituraCartaos\LeituraCartaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeituraCartao extends EditRecord
{
    protected static string $resource = LeituraCartaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
