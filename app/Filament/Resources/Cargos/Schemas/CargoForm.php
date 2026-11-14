<?php

namespace App\Filament\Resources\Cargos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CargoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label('Organizadora')
                    ->relationship('tenant', 'nome')
                    ->required(),
                Select::make('concurso_id')
                    ->relationship('concurso', 'id')
                    ->required(),
                TextInput::make('nome')
                    ->required(),
                TextInput::make('vagas')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
