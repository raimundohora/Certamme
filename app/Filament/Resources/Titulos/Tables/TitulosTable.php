<?php

namespace App\Filament\Resources\Titulos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TitulosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pontos')->label('Pontos')->searchable()->sortable(),
                TextColumn::make('inscricao.nome')->label('Inscricao')->searchable()->sortable(),
                TextColumn::make('tipo.nome')->label('Titulo tipo')->searchable()->sortable(),
                TextColumn::make('status')->label('Status')->badge()->sortable(),
                TextColumn::make('avaliador')->label('Avaliador')->sortable()->toggleable(),
                TextColumn::make('conferente')->label('Conferente')->sortable()->toggleable(),
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
