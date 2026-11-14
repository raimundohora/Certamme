<?php

namespace App\Filament\Resources\Titulos\Schemas;

use App\Enums\StatusTitulo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label('Organizadora')
                    ->relationship('tenant', 'nome')
                    ->required(),
                Select::make('inscricao_id')
                    ->relationship('inscricao', 'id')
                    ->required(),
                TextInput::make('titulo_tipo_id')
                    ->required()
                    ->numeric(),
                TextInput::make('pontos')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options(StatusTitulo::class)
                    ->default('pendente')
                    ->required(),
                TextInput::make('avaliador'),
                TextInput::make('conferente'),
            ]);
    }
}
