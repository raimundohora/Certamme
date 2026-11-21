<?php

namespace App\Filament\Resources\Cargos\Schemas;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CargoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')
                    ->label('Concurso')
                    ->relationship('concurso', 'nome')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nome')
                    ->label('Nome do cargo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('vagas')
                    ->label('Vagas (total)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Total de vagas do cargo. A ampla concorrência recebe o total menos as reservadas às cotas abaixo.'),
                Fieldset::make('Vagas reservadas (cotas)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('vagas_cotas.cota_pcd')
                            ->label('PcD')->numeric()->minValue(0)->default(0),
                        TextInput::make('vagas_cotas.cota_racial')
                            ->label('Racial')->numeric()->minValue(0)->default(0),
                        TextInput::make('vagas_cotas.cota_indigena')
                            ->label('Indígena')->numeric()->minValue(0)->default(0),
                    ]),
                TextInput::make('taxa_inscricao')
                    ->label('Taxa de inscrição (R$)')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('R$')
                    ->helperText('Deixe em branco para inscrição gratuita (isenta de pagamento).'),
            ]);
    }
}
