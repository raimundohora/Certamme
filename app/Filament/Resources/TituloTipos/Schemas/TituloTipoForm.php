<?php

namespace App\Filament\Resources\TituloTipos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloTipoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')->label('Etapa')->relationship('etapa', 'ordem')->searchable()->required(),
                TextInput::make('nome')->label('Nome')->maxLength(255)->required(),
                TextInput::make('pontuacao_maxima')->label('Pontuacao maxima')->maxLength(255),
            ]);
    }
}
