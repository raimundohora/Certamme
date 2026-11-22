<?php

namespace App\Filament\Resources\Heteroidentificacaos\Schemas;

use App\Enums\StatusHeteroidentificacao;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HeteroidentificacaoForm
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
                Select::make('status')
                    ->label('Resultado da verificação')
                    ->options(collect(StatusHeteroidentificacao::cases())
                        ->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all())
                    ->default(StatusHeteroidentificacao::PENDENTE->value)
                    ->required(),
                Textarea::make('parecer')
                    ->label('Parecer da comissão')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('avaliador')
                    ->label('Avaliador / comissão')
                    ->helperText('Preenchido automaticamente com o seu usuário ao decidir, se deixado em branco.'),
                DateTimePicker::make('data_avaliacao')
                    ->label('Data da avaliação')
                    ->helperText('Preenchida automaticamente ao decidir, se deixada em branco.'),
            ]);
    }
}
