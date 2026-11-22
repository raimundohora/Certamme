<?php

namespace App\Filament\Resources\ProvaSubjetivas\Schemas;

use App\Enums\StatusSubjetiva;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProvaSubjetivaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')
                    ->label('Etapa')
                    ->relationship('etapa', 'ordem')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 'Etapa ' . $record->ordem)
                    ->searchable()->preload()->required(),
                Select::make('inscricao_id')
                    ->label('Inscrição')
                    ->relationship('inscricao', 'nome')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->numero_inscricao . ' — ' . $record->nome)
                    ->searchable(['numero_inscricao', 'nome'])->preload()->required(),
                TextInput::make('codigo_controle')
                    ->label('Código de controle (anônimo)')
                    ->helperText('Gerado automaticamente. É por ele que os corretores identificam a folha, sem ver o candidato.')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('nota_final')
                    ->label('Nota final')
                    ->numeric()
                    ->helperText('Calculada na consolidação da dupla correção.')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('status')
                    ->label('Status')
                    ->options(collect(StatusSubjetiva::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all())
                    ->default(StatusSubjetiva::PENDENTE->value)
                    ->required(),
            ]);
    }
}
