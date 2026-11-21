<?php

namespace App\Filament\Resources\Provas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProvaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')->label('Etapa')->relationship('etapa', 'ordem')->searchable()->required(),
                TextInput::make('nome')->label('Nome')->maxLength(255)->required(),
                TextInput::make('caderno')->label('Caderno')->maxLength(255),
                TextInput::make('qtd_questoes')->label('Qtd questoes')->maxLength(255),
            ]);
    }
}
