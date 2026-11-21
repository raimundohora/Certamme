<?php

namespace App\Filament\Resources\LeituraCartaos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeituraCartaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('prova_id')->label('Prova id')->relationship('prova', 'nome')->searchable()->required(),
                Select::make('inscricao_id')->label('Inscricao id')->relationship('inscricao', 'nome')->searchable()->required(),
                TextInput::make('importacao_id')->label('Importacao id')->numeric(),
                TextInput::make('respostas')->label('Respostas')->maxLength(255),
                TextInput::make('origem')->label('Origem')->maxLength(255),
            ]);
    }
}
