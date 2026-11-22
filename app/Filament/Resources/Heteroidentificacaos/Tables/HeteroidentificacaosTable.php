<?php

namespace App\Filament\Resources\Heteroidentificacaos\Tables;

use App\Enums\StatusHeteroidentificacao;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HeteroidentificacaosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('inscricao.numero_inscricao')->label('Nº inscrição')->searchable()->sortable(),
                TextColumn::make('inscricao.nome')->label('Candidato')->searchable()->sortable(),
                TextColumn::make('inscricao.modalidade')
                    ->label('Cota declarada')
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\Modalidade ? $state->label() : (string) $state)
                    ->badge(),
                TextColumn::make('status')
                    ->label('Resultado')
                    ->badge()
                    ->formatStateUsing(fn (StatusHeteroidentificacao $state) => $state->label())
                    ->color(fn (StatusHeteroidentificacao $state) => $state->cor()),
                TextColumn::make('avaliador')->label('Avaliador')->toggleable(),
                TextColumn::make('data_avaliacao')->label('Avaliado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Resultado')
                    ->options(collect(StatusHeteroidentificacao::cases())
                        ->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all()),
            ])
            ->recordActions([
                EditAction::make()->label('Avaliar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
