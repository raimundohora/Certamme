<?php

namespace App\Filament\Resources\Alocacaos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AlocacaoForm
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
                Select::make('sala_id')
                    ->relationship('sala', 'id')
                    ->required(),
                TextInput::make('carteira')
                    ->required()
                    ->numeric(),
            ]);
    }
}
