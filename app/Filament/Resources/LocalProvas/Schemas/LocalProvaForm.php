<?php

namespace App\Filament\Resources\LocalProvas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LocalProvaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
                Select::make('concurso_id')
                    ->relationship('concurso', 'id')
                    ->required(),
                TextInput::make('nome')
                    ->required(),
                TextInput::make('endereco'),
                TextInput::make('cidade'),
            ]);
    }
}
