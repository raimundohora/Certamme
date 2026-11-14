<?php

namespace App\Filament\Resources\Salas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SalaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
                TextInput::make('local_prova_id')
                    ->required()
                    ->numeric(),
                TextInput::make('nome')
                    ->required(),
                TextInput::make('capacidade')
                    ->required()
                    ->numeric()
                    ->default(30),
            ]);
    }
}
