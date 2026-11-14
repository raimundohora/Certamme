<?php

namespace App\Filament\Resources\Resultados\Schemas;

use Filament\Forms\Components\DateTimePicker;
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
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
                Select::make('inscricao_id')
                    ->relationship('inscricao', 'id')
                    ->required(),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('acertos')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('eliminado')
                    ->required(),
                TextInput::make('classificacao')
                    ->numeric(),
                DateTimePicker::make('calculado_em'),
            ]);
    }
}
