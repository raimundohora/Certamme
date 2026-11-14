<?php

namespace App\Filament\Resources\Provas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProvaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
                TextInput::make('caderno'),
                TextInput::make('qtd_questoes')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
