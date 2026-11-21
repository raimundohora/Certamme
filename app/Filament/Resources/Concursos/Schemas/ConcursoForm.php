<?php

namespace App\Filament\Resources\Concursos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConcursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->label('Nome do concurso')
                    ->required()
                    ->maxLength(255),
                TextInput::make('ano')
                    ->label('Ano')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100),
                Select::make('situacao')
                    ->label('Situação')
                    ->options([
                        'rascunho' => 'Rascunho',
                        'aberto' => 'Inscrições abertas',
                        'em_andamento' => 'Em andamento',
                        'homologado' => 'Homologado',
                        'finalizado' => 'Finalizado',
                        'suspenso' => 'Suspenso',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('rascunho')
                    ->required(),
            ]);
    }
}
