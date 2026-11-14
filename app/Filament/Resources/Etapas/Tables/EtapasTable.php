<?php

namespace App\Filament\Resources\Etapas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EtapasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.nome')
                    ->label('Organizadora')
                    ->searchable(),
                TextColumn::make('concurso.id')
                    ->searchable(),
                TextColumn::make('cargo.id')
                    ->searchable(),
                TextColumn::make('ordem')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->badge()
                    ->searchable(),
                IconColumn::make('eliminatoria')
                    ->boolean(),
                IconColumn::make('classificatoria')
                    ->boolean(),
                TextColumn::make('nota_minima')
                    ->numeric()
                    ->sortable(),
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
