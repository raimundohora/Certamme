<?php

namespace App\Filament\Resources\Notas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NotaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->required(),
                Select::make('etapa_id')->label('Etapa')->relationship('etapa', 'ordem')->searchable()->required(),
                TextInput::make('disciplina')->label('Disciplina')->maxLength(255),
                TextInput::make('acertos')->label('Acertos')->numeric(),
                TextInput::make('nota_bruta')->label('Nota bruta')->maxLength(255),
                TextInput::make('nota_final')->label('Nota final')->maxLength(255),
                Toggle::make('eliminado')->label('Eliminado'),
                DatePicker::make('calculada_em')->label('Calculada em'),
                TextInput::make('override')->label('Override')->maxLength(255),
                TextInput::make('nota_override')->label('Nota override')->maxLength(255),
                TextInput::make('override_motivo')->label('Override motivo')->maxLength(255),
                TextInput::make('override_por')->label('Override por')->maxLength(255),
            ]);
    }
}
