<?php

namespace App\Filament\Resources\ProvaSubjetivas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProvaSubjetivasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo_controle')->label('Codigo controle')->searchable()->sortable(),
                TextColumn::make('etapa.ordem')->label('Etapa')->searchable()->sortable(),
                TextColumn::make('inscricao.nome')->label('Inscricao')->searchable()->sortable(),
                TextColumn::make('nota_final')->label('Nota final')->sortable()->toggleable(),
                TextColumn::make('status')->label('Status')->badge()->sortable(),
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
