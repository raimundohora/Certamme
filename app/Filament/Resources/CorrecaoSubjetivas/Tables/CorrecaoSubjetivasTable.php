<?php

namespace App\Filament\Resources\CorrecaoSubjetivas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CorrecaoSubjetivasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('prova.codigo_controle')->label('Folha (código)')->searchable()->sortable()->weight('bold'),
                TextColumn::make('prova.etapa.ordem')->label('Etapa')->sortable()->toggleable(),
                TextColumn::make('corretor')->label('Corretor(a)')->searchable()->sortable(),
                TextColumn::make('nota')->label('Nota')->numeric(2)->sortable(),
                TextColumn::make('created_at')->label('Registrada em')->dateTime('d/m/Y H:i')->sortable(),
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
