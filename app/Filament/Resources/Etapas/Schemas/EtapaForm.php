<?php

namespace App\Filament\Resources\Etapas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Select::make('config.desempate')
                    ->label('Critérios de desempate (na ordem)')
                    ->multiple()
                    ->options([
                        'maior_idade'      => 'Maior idade',
                        'mais_acertos'     => 'Mais acertos',
                        'numero_inscricao' => 'Nº de inscrição (menor)',
                    ])
                    ->default(['maior_idade', 'mais_acertos', 'numero_inscricao'])
                    ->helperText('Aplicados após a nota, na ordem em que forem selecionados. Padrão legal: maior idade primeiro.')
                    ->columnSpanFull(),
            ]);
    }
}
