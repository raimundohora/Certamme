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
                Select::make('inscricao_id')->label('Inscricao')->relationship('inscricao', 'nome')->searchable()->preload()->required(),
                Select::make('etapa_id')->label('Etapa')->relationship('etapa', 'ordem')->searchable()->preload()->required(),
                TextInput::make('disciplina')->label('Disciplina')->maxLength(255),
                TextInput::make('acertos')->label('Acertos')->numeric(),
                TextInput::make('nota_bruta')->label('Nota bruta')->numeric(),
                TextInput::make('nota_final')->label('Nota final')->numeric(),
                Toggle::make('eliminado')->label('Eliminado'),
                DatePicker::make('calculada_em')->label('Calculada em'),
                Toggle::make('override')->label('Override'),
                TextInput::make('nota_override')->label('Nota override')->numeric(),
                TextInput::make('override_motivo')->label('Override motivo')->maxLength(255),
                TextInput::make('override_por')->label('Override por')->maxLength(255),
            ]);
    }
}
