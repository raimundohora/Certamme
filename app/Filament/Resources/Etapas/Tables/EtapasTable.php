<?php

namespace App\Filament\Resources\Etapas\Tables;

use App\Models\Etapa;
use App\Services\ApuracaoService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EtapasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ordem')->label('Ordem')->searchable()->sortable(),
                TextColumn::make('concurso.nome')->label('Concurso')->searchable()->sortable(),
                TextColumn::make('cargo.nome')->label('Cargo')->searchable()->sortable(),
                TextColumn::make('tipo')->label('Tipo')->badge()->sortable(),
                IconColumn::make('eliminatoria')->label('Eliminatoria')->boolean(),
                IconColumn::make('classificatoria')->label('Classificatoria')->boolean(),
                TextColumn::make('nota_minima')->label('Nota minima')->sortable()->toggleable(),
                TextColumn::make('peso')->label('Peso')->sortable()->toggleable(),
                TextColumn::make('resultados_count')->label('Resultados')->counts('resultados')->toggleable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('apurar')
                    ->label('Apurar resultado')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Soma as notas desta etapa, aplica a nota mínima/eliminação e gera a classificação (grava em Resultados). Pode rodar de novo quando quiser.')
                    ->action(function (Etapa $record) {
                        $r = app(ApuracaoService::class)->apurarEtapa($record);
                        Notification::make()
                            ->title('Apuração concluída')
                            ->body("Apurados: {$r['apurados']} · Eliminados: {$r['eliminados']} · Classificados: {$r['classificados']}")
                            ->success()
                            ->send();
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
