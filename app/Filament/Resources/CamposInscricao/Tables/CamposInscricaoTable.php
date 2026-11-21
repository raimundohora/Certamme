<?php

namespace App\Filament\Resources\CamposInscricao\Tables;

use App\Enums\TipoCampo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CamposInscricaoTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('ordem')
            ->columns([
                TextColumn::make('ordem')->label('#')->sortable(),
                TextColumn::make('rotulo')->label('Rótulo')->searchable()->sortable(),
                TextColumn::make('concurso.nome')->label('Concurso')->searchable()->sortable(),
                TextColumn::make('tipo')->label('Tipo')->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof TipoCampo ? $state->label() : (string) $state),
                IconColumn::make('obrigatorio')->label('Obrigatório')->boolean(),
                IconColumn::make('visivel')->label('Visível')->boolean(),
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
