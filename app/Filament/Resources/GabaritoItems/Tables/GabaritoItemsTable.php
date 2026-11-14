<?php

namespace App\Filament\Resources\GabaritoItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GabaritoItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.id')
                    ->searchable(),
                TextColumn::make('prova.id')
                    ->searchable(),
                TextColumn::make('numero')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('disciplina')
                    ->searchable(),
                TextColumn::make('resposta_correta')
                    ->searchable(),
                TextColumn::make('situacao')
                    ->badge()
                    ->searchable(),
                TextColumn::make('resposta_nova')
                    ->searchable(),
                TextColumn::make('peso')
                    ->numeric()
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
