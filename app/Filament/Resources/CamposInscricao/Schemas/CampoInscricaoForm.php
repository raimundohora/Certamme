<?php

namespace App\Filament\Resources\CamposInscricao\Schemas;

use App\Enums\TipoCampo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CampoInscricaoForm
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
                TextInput::make('rotulo')
                    ->label('Rótulo (a pergunta que o candidato vê)')
                    ->required()
                    ->maxLength(255),
                Select::make('tipo')
                    ->label('Tipo de campo')
                    ->options(collect(TipoCampo::cases())->mapWithKeys(fn (TipoCampo $t) => [$t->value => $t->label()])->all())
                    ->default('texto')
                    ->required()
                    ->live(),
                TagsInput::make('opcoes')
                    ->label('Opções da lista')
                    ->helperText('Digite cada opção e pressione Enter.')
                    ->visible(fn (Get $get): bool => $get('tipo') === 'select'),
                Toggle::make('obrigatorio')
                    ->label('Obrigatório')
                    ->default(false),
                Toggle::make('visivel')
                    ->label('Visível na inscrição')
                    ->default(true),
                TextInput::make('ordem')
                    ->label('Ordem de exibição')
                    ->numeric()
                    ->default(0),
                TextInput::make('ajuda')
                    ->label('Texto de ajuda (opcional)')
                    ->maxLength(255),
            ]);
    }
}
