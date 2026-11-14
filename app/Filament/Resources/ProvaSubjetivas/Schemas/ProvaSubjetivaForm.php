<?php

namespace App\Filament\Resources\ProvaSubjetivas\Schemas;

use App\Enums\StatusSubjetiva;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProvaSubjetivaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label('Organizadora')
                    ->relationship('tenant', 'nome')
                    ->required(),
                Select::make('etapa_id')
                    ->relationship('etapa', 'id')
                    ->required(),
                Select::make('inscricao_id')
                    ->relationship('inscricao', 'id')
                    ->required(),
                TextInput::make('codigo_controle')
                    ->required(),
                TextInput::make('nota_final')
                    ->numeric(),
                Select::make('status')
                    ->options(StatusSubjetiva::class)
                    ->default('pendente')
                    ->required(),
            ]);
    }
}
