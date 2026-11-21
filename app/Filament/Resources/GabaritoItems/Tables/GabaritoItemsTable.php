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
                TextColumn::make('numero')->label('Numero')->searchable()->sortable(),
                TextColumn::make('prova.nome')->label('Prova')->searchable()->sortable(),
                TextColumn::make('disciplina')->label('Disciplina')->sortable()->toggleable(),
                TextColumn::make('resposta_correta')->label('Resposta correta')->sortable()->toggleable(),
                TextColumn::make('situacao')->label('Situacao')->badge()->sortable(),
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
