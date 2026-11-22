<?php

namespace App\Filament\Resources\TituloTipos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloTipoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')
                    ->label('Etapa de títulos')
                    ->relationship('etapa', 'ordem')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 'Etapa ' . $record->ordem . ' — ' . ($record->tipo?->label() ?? $record->tipo))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nome')
                    ->label('Nome do título')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('pontuacao_maxima')
                    ->label('Pontuação máxima (teto)')
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->helperText('Teto de pontos deste tipo por candidato na apuração. 0 = sem teto.'),
            ]);
    }
}
