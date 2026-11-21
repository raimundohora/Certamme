<?php

namespace App\Filament\Resources\Salas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SalaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('local_prova_id')->label('Local prova id')->numeric(),
                TextInput::make('nome')->label('Nome')->maxLength(255)->required(),
                TextInput::make('capacidade')->label('Capacidade')->numeric(),
            ]);
    }
}
