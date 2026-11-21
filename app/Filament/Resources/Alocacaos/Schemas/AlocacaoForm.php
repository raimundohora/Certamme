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
                Select::make('etapa_id')->label('Etapa')->relationship('etapa', 'ordem')->searchable()->preload()->required(),
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->preload()->required(),
                Select::make('sala_id')->label('Sala')->relationship('sala', 'nome')->searchable()->preload()->required(),
                TextInput::make('carteira')->label('Carteira')->maxLength(255)->required(),
            ]);
    }
}
