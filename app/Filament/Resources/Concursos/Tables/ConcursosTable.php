<?php

namespace App\Filament\Resources\Concursos\Tables;

use App\Support\ConcursoAtual;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConcursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Concurso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ano')
                    ->label('Ano')
                    ->sortable(),
                TextColumn::make('situacao')
                    ->label('Situação')
                    ->badge()
                    ->sortable(),
                TextColumn::make('cargos_count')
                    ->label('Cargos')
                    ->counts('cargos')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('gerenciar')
                    ->label('Gerenciar')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->action(function ($record) {
                        ConcursoAtual::set($record->id);
                        return redirect('/admin');
                    }),
                Action::make('sair')
                    ->label('Sair')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('gray')
                    ->visible(fn ($record) => ConcursoAtual::id() === (int) $record->id)
                    ->action(function () {
                        ConcursoAtual::clear();
                        return redirect('/admin');
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
