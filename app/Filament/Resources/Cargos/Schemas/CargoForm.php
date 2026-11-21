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
                Select::make('concurso_id')
                    ->label('Concurso')
                    ->relationship('concurso', 'nome')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nome')
                    ->label('Nome do cargo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('vagas')
                    ->label('Vagas')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ]);
    }
}
