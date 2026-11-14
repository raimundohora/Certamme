<?php

namespace App\Filament\Resources\Notas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NotaForm
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
                Select::make('etapa_id')
                    ->relationship('etapa', 'id')
                    ->required(),
                TextInput::make('disciplina'),
                TextInput::make('acertos')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('nota_bruta')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('nota_final')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('eliminado')
                    ->required(),
                DateTimePicker::make('calculada_em'),
            ]);
    }
}
