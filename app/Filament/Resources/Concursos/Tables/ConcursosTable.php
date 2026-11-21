<?php

namespace App\Filament\Resources\Concursos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConcursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Concurso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ano')
                    ->label('Ano')
                    ->sortable(),
                TextColumn::make('situacao')
                    ->label('Situação')
                    ->badge()
                    ->sortable(),
                TextColumn::make('cargos_count')
                    ->label('Cargos')
                    ->counts('cargos')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
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
