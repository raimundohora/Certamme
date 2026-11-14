<?php

namespace App\Filament\Resources\Etapas\Schemas;

use App\Enums\TipoEtapa;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EtapaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'id')
                    ->required(),
                Select::make('concurso_id')
                    ->relationship('concurso', 'id')
                    ->required(),
                Select::make('cargo_id')
                    ->relationship('cargo', 'id'),
                TextInput::make('ordem')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('tipo')
                    ->options(TipoEtapa::class)
                    ->required(),
                Toggle::make('eliminatoria')
                    ->required(),
                Toggle::make('classificatoria')
                    ->required(),
                TextInput::make('nota_minima')
                    ->numeric(),
                TextInput::make('peso')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('config'),
            ]);
    }
}
