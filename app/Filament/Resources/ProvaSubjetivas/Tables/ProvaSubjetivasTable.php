<?php

namespace App\Filament\Resources\ProvaSubjetivas\Tables;

use App\Enums\StatusSubjetiva;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProvaSubjetivasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('codigo_controle')->label('Código')->searchable()->sortable()->weight('bold'),
                TextColumn::make('etapa.ordem')->label('Etapa')->sortable(),
                TextColumn::make('correcoes_count')->label('Correções')->counts('correcoes')->badge(),
                TextColumn::make('nota_final')->label('Nota final')->numeric(2)->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StatusSubjetiva $state) => ucfirst($state->value))
                    ->color(fn (StatusSubjetiva $state) => match ($state) {
                        StatusSubjetiva::CORRIGIDA  => 'success',
                        StatusSubjetiva::DIVERGENTE => 'danger',
                        default                     => 'warning',
                    }),
                // Candidato oculto por padrão (sigilo da correção); exibível quando necessário.
                TextColumn::make('inscricao.nome')->label('Candidato')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('inscricao.numero_inscricao')->label('Nº inscrição')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(StatusSubjetiva::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all()),
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
