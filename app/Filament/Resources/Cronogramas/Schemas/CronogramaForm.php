<?php

namespace App\Filament\Resources\Cronogramas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CronogramaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')->label('Concurso id')->relationship('concurso', 'nome')->searchable()->required(),
                TextInput::make('evento')->label('Evento')->maxLength(255),
                DatePicker::make('data')->label('Data'),
            ]);
    }
}
