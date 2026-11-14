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
                Select::make('tenant_id')
                    ->label('Organizadora')
                    ->relationship('tenant', 'nome')
                    ->required(),
                TextInput::make('nome')
                    ->required(),
                TextInput::make('ano')
                    ->numeric(),
                TextInput::make('situacao')
                    ->required()
                    ->default('rascunho'),
            ]);
    }
}
