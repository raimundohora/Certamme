<?php

namespace App\Filament\Resources\Documentos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DocumentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->preload()->required(),
                TextInput::make('tipo')->label('Tipo')->maxLength(255),
                TextInput::make('arquivo')->label('Arquivo')->maxLength(255),
                TextInput::make('status')->label('Status')->maxLength(255),
                Textarea::make('observacao')->label('Observacao')->rows(3)->columnSpanFull(),
            ]);
    }
}
