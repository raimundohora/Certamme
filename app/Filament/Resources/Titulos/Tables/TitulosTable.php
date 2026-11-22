<?php

namespace App\Filament\Resources\Titulos\Tables;

use App\Enums\StatusTitulo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TitulosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('inscricao.numero_inscricao')->label('Nº inscrição')->searchable()->sortable(),
                TextColumn::make('inscricao.nome')->label('Candidato')->searchable()->sortable(),
                TextColumn::make('tipo.nome')->label('Tipo de título')->searchable()->sortable(),
                TextColumn::make('tipo.pontuacao_maxima')->label('Teto')->numeric(2)->toggleable(),
                TextColumn::make('pontos')->label('Pontos')->numeric(2)->sortable(),
                TextColumn::make('status')->label('Status')->badge()->sortable(),
                TextColumn::make('avaliador')->label('Avaliador')->toggleable(),
                TextColumn::make('conferente')->label('Conferente')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(StatusTitulo::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
