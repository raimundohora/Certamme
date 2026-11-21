<?php

namespace App\Filament\Resources\Inscricaos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InscricaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')->label('Concurso id')->relationship('concurso', 'nome')->searchable()->required(),
                Select::make('candidato_id')->label('Candidato id')->relationship('candidato', 'nome')->searchable()->required(),
                Select::make('cargo_id')->label('Cargo id')->relationship('cargo', 'nome')->searchable()->required(),
                TextInput::make('numero_inscricao')->label('Numero inscricao')->numeric(),
                TextInput::make('nome')->label('Nome')->maxLength(255)->required(),
                TextInput::make('modalidade')->label('Modalidade')->maxLength(255),
                TextInput::make('status')->label('Status')->maxLength(255),
            ]);
    }
}
