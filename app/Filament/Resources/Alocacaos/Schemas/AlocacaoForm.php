<?php

namespace App\Filament\Resources\Alocacaos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AlocacaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')->label('Etapa id')->relationship('etapa', 'ordem')->searchable()->required(),
                Select::make('inscricao_id')->label('Inscricao id')->relationship('inscricao', 'nome')->searchable()->required(),
                Select::make('sala_id')->label('Sala id')->relationship('sala', 'nome')->searchable()->required(),
                TextInput::make('carteira')->label('Carteira')->maxLength(255),
            ]);
    }
}
