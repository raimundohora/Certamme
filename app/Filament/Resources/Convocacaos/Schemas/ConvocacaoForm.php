<?php

namespace App\Filament\Resources\Convocacaos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ConvocacaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')->label('Etapa id')->relationship('etapa', 'ordem')->searchable()->required(),
                Select::make('inscricao_id')->label('Inscricao id')->relationship('inscricao', 'nome')->searchable()->required(),
                Toggle::make('publicado')->label('Publicado'),
                Textarea::make('observacao')->label('Observacao')->rows(3)->columnSpanFull(),
            ]);
    }
}
