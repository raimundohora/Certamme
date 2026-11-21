<?php

namespace App\Filament\Resources\ProvaSubjetivas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProvaSubjetivaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')->label('Etapa id')->relationship('etapa', 'ordem')->searchable()->required(),
                Select::make('inscricao_id')->label('Inscricao id')->relationship('inscricao', 'nome')->searchable()->required(),
                TextInput::make('codigo_controle')->label('Codigo controle')->maxLength(255),
                TextInput::make('nota_final')->label('Nota final')->maxLength(255),
                TextInput::make('status')->label('Status')->maxLength(255),
            ]);
    }
}
