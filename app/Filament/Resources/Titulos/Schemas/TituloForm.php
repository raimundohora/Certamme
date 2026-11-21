<?php

namespace App\Filament\Resources\Titulos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->required(),
                TextInput::make('titulo_tipo_id')->label('Titulo tipo')->numeric(),
                TextInput::make('pontos')->label('Pontos')->maxLength(255),
                TextInput::make('status')->label('Status')->maxLength(255),
                TextInput::make('avaliador')->label('Avaliador')->maxLength(255),
                TextInput::make('conferente')->label('Conferente')->maxLength(255),
            ]);
    }
}
