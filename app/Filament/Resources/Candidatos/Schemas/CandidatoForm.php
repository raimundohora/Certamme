<?php

namespace App\Filament\Resources\Candidatos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CandidatoForm
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
                TextInput::make('cpf')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                DatePicker::make('data_nascimento'),
                TextInput::make('telefone')
                    ->tel(),
            ]);
    }
}
