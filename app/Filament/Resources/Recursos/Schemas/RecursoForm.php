<?php

namespace App\Filament\Resources\Recursos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RecursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->required(),
                Select::make('etapa_id')->label('Etapa')->relationship('etapa', 'ordem')->searchable()->required(),
                TextInput::make('tipo')->label('Tipo')->maxLength(255),
                TextInput::make('questao')->label('Questao')->maxLength(255),
                Textarea::make('texto')->label('Texto')->rows(3)->columnSpanFull(),
                TextInput::make('status')->label('Status')->maxLength(255),
                Textarea::make('resposta')->label('Resposta')->rows(3)->columnSpanFull(),
            ]);
    }
}
