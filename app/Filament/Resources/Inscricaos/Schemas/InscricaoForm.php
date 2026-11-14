<?php

namespace App\Filament\Resources\Inscricaos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InscricaoForm
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
                Select::make('cargo_id')
                    ->relationship('cargo', 'id'),
                TextInput::make('numero_inscricao')
                    ->required(),
                TextInput::make('nome'),
            ]);
    }
}
