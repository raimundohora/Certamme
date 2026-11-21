<?php

namespace App\Filament\Resources\Alocacaos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlocacaosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('carteira')->label('Carteira')->searchable()->sortable(),
                TextColumn::make('etapa.ordem')->label('Etapa')->searchable()->sortable(),
                TextColumn::make('inscricao.nome')->label('Inscricao')->searchable()->sortable(),
                TextColumn::make('sala.nome')->label('Sala')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
