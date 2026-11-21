<?php

namespace App\Filament\Resources\LocalProvas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocalProvaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')->label('Concurso')->relationship('concurso', 'nome')->searchable()->preload()->required(),
                TextInput::make('nome')->label('Nome')->maxLength(255)->required(),
                TextInput::make('endereco')->label('Endereco')->maxLength(255),
                TextInput::make('cidade')->label('Cidade')->maxLength(255),
            ]);
    }
}
