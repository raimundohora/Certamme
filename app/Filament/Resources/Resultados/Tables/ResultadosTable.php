<?php

namespace App\Filament\Resources\Resultados\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResultadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('classificacao')
            ->columns([
                TextColumn::make('classificacao')->label('Classif. geral')->sortable()
                    ->placeholder('—')->alignCenter()->badge()->color('primary'),
                TextColumn::make('classificacao_cota')->label('Classif. cota')->sortable()
                    ->placeholder('—')->alignCenter()->toggleable(),
                TextColumn::make('inscricao.numero_inscricao')->label('Nº insc.')->searchable()->toggleable(),
                TextColumn::make('inscricao.nome')->label('Candidato')->searchable()->sortable(),
                TextColumn::make('inscricao.modalidade')->label('Modalidade')->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state)->toggleable(),
                TextColumn::make('etapa.ordem')->label('Etapa')->sortable()->alignCenter(),
                TextColumn::make('total')->label('Total')->numeric(2)->sortable(),
                TextColumn::make('acertos')->label('Acertos')->sortable()->toggleable(),
                IconColumn::make('eliminado')->label('Eliminado')->boolean()
                    ->trueColor('danger')->falseColor('success'),
                TextColumn::make('calculado_em')->label('Apurado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(),
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
