<?php

namespace App\Filament\Resources\Titulos\Schemas;

use App\Enums\StatusTitulo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inscricao_id')
                    ->label('Inscrição')
                    ->relationship('inscricao', 'nome')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->numero_inscricao . ' — ' . $record->nome)
                    ->searchable(['numero_inscricao', 'nome'])
                    ->preload()
                    ->required(),
                Select::make('titulo_tipo_id')
                    ->label('Tipo de título')
                    ->relationship('tipo', 'nome')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('pontos')
                    ->label('Pontos')
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->helperText('Pontuação atribuída a este título (respeitando o teto do tipo na apuração).'),
                Select::make('status')
                    ->label('Status')
                    ->options(collect(StatusTitulo::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all())
                    ->default(StatusTitulo::PENDENTE->value)
                    ->required(),
                TextInput::make('avaliador')->label('Avaliador')->maxLength(255),
                TextInput::make('conferente')->label('Conferente')->maxLength(255),
            ]);
    }
}
