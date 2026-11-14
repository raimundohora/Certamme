<?php

namespace App\Filament\Resources\TituloTipos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloTipoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'id')
                    ->required(),
                Select::make('etapa_id')
                    ->relationship('etapa', 'id')
                    ->required(),
                TextInput::make('nome')
                    ->required(),
                TextInput::make('pontuacao_maxima')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
