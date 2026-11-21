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
                Select::make('concurso_id')->label('Concurso')->relationship('concurso', 'nome')->searchable()->preload()->required(),
                Select::make('candidato_id')->label('Candidato')->relationship('candidato', 'nome')->searchable()->preload()->required(),
                Select::make('cargo_id')->label('Cargo')->relationship('cargo', 'nome')->searchable()->preload(),
                TextInput::make('numero_inscricao')->label('Numero inscricao')->numeric()->required(),
                TextInput::make('nome')->label('Nome')->maxLength(255)->required(),
                TextInput::make('modalidade')->label('Modalidade')->maxLength(255),
                TextInput::make('status')->label('Status')->maxLength(255),
            ]);
    }
}
