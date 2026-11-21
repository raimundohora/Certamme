<?php

namespace App\Filament\Resources\Notas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('disciplina')->label('Disciplina')->searchable()->sortable(),
                TextColumn::make('inscricao.nome')->label('Inscricao')->searchable()->sortable(),
                TextColumn::make('etapa.ordem')->label('Etapa')->searchable()->sortable(),
                TextColumn::make('acertos')->label('Acertos')->sortable()->toggleable(),
                TextColumn::make('nota_bruta')->label('Nota bruta')->sortable()->toggleable(),
                TextColumn::make('nota_final')->label('Nota final')->sortable()->toggleable(),
                IconColumn::make('eliminado')->label('Eliminado')->boolean(),
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
