<?php

namespace App\Filament\Resources\Convocacaos\Tables;

use App\Models\Convocacao;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConvocacaosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('etapa_id')
            ->columns([
                IconColumn::make('publicado')->label('Publicado')->boolean()
                    ->trueColor('success')->falseColor('gray')->sortable(),
                TextColumn::make('etapa.ordem')->label('Etapa')->alignCenter()->sortable(),
                TextColumn::make('inscricao.numero_inscricao')->label('Nº insc.')->searchable()->toggleable(),
                TextColumn::make('inscricao.nome')->label('Candidato')->searchable()->sortable(),
                TextColumn::make('observacao')->label('Observação')->wrap()->toggleable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('togglePublicado')
                    ->label(fn (Convocacao $record) => $record->publicado ? 'Despublicar' : 'Publicar')
                    ->icon(fn (Convocacao $record) => $record->publicado ? 'heroicon-o-eye-slash' : 'heroicon-o-megaphone')
                    ->color(fn (Convocacao $record) => $record->publicado ? 'gray' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Convocacao $record) {
                        $record->update(['publicado' => ! $record->publicado]);
                        Notification::make()
                            ->title($record->publicado ? 'Convocação publicada' : 'Convocação despublicada')
                            ->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
