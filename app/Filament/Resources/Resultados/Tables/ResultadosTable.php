<?php

namespace App\Filament\Resources\Resultados\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResultadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.id')
                    ->searchable(),
                TextColumn::make('etapa.id')
                    ->searchable(),
                TextColumn::make('inscricao.id')
                    ->searchable(),
                TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('acertos')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('eliminado')
                    ->boolean(),
                TextColumn::make('classificacao')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('calculado_em')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
