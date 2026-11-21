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
                Select::make('prova_id')->label('Prova')->relationship('prova', 'nome')->searchable()->preload()->required(),
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->preload()->required(),
                Select::make('importacao_id')->label('Importacao')->options(\App\Models\Importacao::query()->pluck('arquivo', 'id'))->searchable(),
                TextInput::make('respostas')->label('Respostas')->maxLength(255)->required(),
                TextInput::make('origem')->label('Origem')->maxLength(255),
            ]);
    }
}
