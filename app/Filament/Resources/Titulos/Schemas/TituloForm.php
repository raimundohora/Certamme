<?php

namespace App\Filament\Resources\Titulos\Schemas;

use App\Enums\StatusTitulo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
                TextInput::make('avaliador'),
                TextInput::make('conferente'),
            ]);
    }
}
