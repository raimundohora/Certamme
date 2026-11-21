<?php

namespace App\Filament\Resources\Etapas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EtapaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')->label('Concurso')->relationship('concurso', 'nome')->searchable()->preload()->required(),
                Select::make('cargo_id')->label('Cargo')->relationship('cargo', 'nome')->searchable()->preload(),
                TextInput::make('ordem')->label('Ordem')->numeric(),
                TextInput::make('tipo')->label('Tipo')->maxLength(255)->required(),
                Toggle::make('eliminatoria')->label('Eliminatoria'),
                Toggle::make('classificatoria')->label('Classificatoria'),
                TextInput::make('nota_minima')->label('Nota minima')->numeric(),
                TextInput::make('peso')->label('Peso')->numeric(),
                Textarea::make('config')->label('Config')->rows(3)->columnSpanFull(),
            ]);
    }
}
