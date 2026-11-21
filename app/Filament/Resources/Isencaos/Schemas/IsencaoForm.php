<?php

namespace App\Filament\Resources\Isencaos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IsencaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->required(),
                TextInput::make('nis')->label('Nis')->maxLength(255),
                Textarea::make('justificativa')->label('Justificativa')->rows(3)->columnSpanFull(),
                TextInput::make('status')->label('Status')->maxLength(255),
                Textarea::make('motivo')->label('Motivo')->rows(3)->columnSpanFull(),
            ]);
    }
}
