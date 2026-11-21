<?php

namespace App\Filament\Resources\Etapas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EtapaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')->label('Concurso id')->relationship('concurso', 'nome')->searchable()->required(),
                Select::make('cargo_id')->label('Cargo id')->relationship('cargo', 'nome')->searchable()->required(),
                TextInput::make('ordem')->label('Ordem')->numeric(),
                TextInput::make('tipo')->label('Tipo')->maxLength(255),
                TextInput::make('eliminatoria')->label('Eliminatoria')->maxLength(255),
                TextInput::make('classificatoria')->label('Classificatoria')->maxLength(255),
                TextInput::make('nota_minima')->label('Nota minima')->maxLength(255),
                TextInput::make('peso')->label('Peso')->numeric(),
                TextInput::make('config')->label('Config')->maxLength(255),
            ]);
    }
}
