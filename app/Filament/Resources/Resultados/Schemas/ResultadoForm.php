<?php

namespace App\Filament\Resources\Resultados\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ResultadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')->label('Etapa')->relationship('etapa', 'ordem')->searchable()->required(),
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->required(),
                TextInput::make('total')->label('Total')->numeric(),
                TextInput::make('acertos')->label('Acertos')->numeric(),
                Toggle::make('eliminado')->label('Eliminado'),
                TextInput::make('classificacao')->label('Classificacao')->numeric(),
                DatePicker::make('calculado_em')->label('Calculado em'),
            ]);
    }
}
