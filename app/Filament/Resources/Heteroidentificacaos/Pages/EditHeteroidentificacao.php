<?php

namespace App\Filament\Resources\Heteroidentificacaos\Pages;

use App\Filament\Resources\Heteroidentificacaos\HeteroidentificacaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHeteroidentificacao extends EditRecord
{
    protected static string $resource = HeteroidentificacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
