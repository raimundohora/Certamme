<?php

namespace App\Filament\Resources\GabaritoItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GabaritoItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('prova_id')->label('Prova id')->relationship('prova', 'nome')->searchable()->required(),
                TextInput::make('numero')->label('Numero')->maxLength(255),
                TextInput::make('disciplina')->label('Disciplina')->maxLength(255),
                TextInput::make('resposta_correta')->label('Resposta correta')->maxLength(255),
                TextInput::make('situacao')->label('Situacao')->maxLength(255),
                TextInput::make('resposta_nova')->label('Resposta nova')->maxLength(255),
                TextInput::make('peso')->label('Peso')->numeric(),
            ]);
    }
}
