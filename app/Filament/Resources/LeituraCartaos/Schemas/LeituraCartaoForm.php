<?php

namespace App\Filament\Resources\LeituraCartaos\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class LeituraCartaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('prova_id')
                    ->label('Prova')
                    ->relationship('prova', 'nome')
                    ->searchable()
                    ->required(),
                Select::make('inscricao_id')
                    ->label('Inscrição')
                    ->relationship('inscricao', 'numero_inscricao')
                    ->searchable()
                    ->required(),
                KeyValue::make('respostas')
                    ->label('Respostas (questão → letra)')
                    ->keyLabel('Questão')
                    ->valueLabel('Resposta')
                    ->reorderable(false),
            ]);
    }
}
