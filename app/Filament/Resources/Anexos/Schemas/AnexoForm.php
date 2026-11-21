<?php

namespace App\Filament\Resources\Anexos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnexoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')->label('Concurso id')->relationship('concurso', 'nome')->searchable()->required(),
                TextInput::make('titulo')->label('Titulo')->maxLength(255)->required(),
                TextInput::make('grupo')->label('Grupo')->maxLength(255),
                TextInput::make('arquivo')->label('Arquivo')->maxLength(255),
                Toggle::make('publicado')->label('Publicado'),
            ]);
    }
}
