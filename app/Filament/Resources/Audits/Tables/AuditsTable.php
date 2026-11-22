<?php

namespace App\Filament\Resources\Audits\Tables;

use App\Filament\Resources\Audits\AuditResource;
use App\Models\Audit;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data/hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('event')
                    ->label('Ação')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => AuditResource::eventoLabel($state))
                    ->color(fn (?string $state) => match ($state) {
                        'created'  => 'success',
                        'updated'  => 'warning',
                        'deleted'  => 'danger',
                        default    => 'gray',
                    }),
                TextColumn::make('auditable_type')
                    ->label('Registro')
                    ->formatStateUsing(fn (?string $state, Audit $record) => AuditResource::modeloLabel($state).' #'.$record->auditable_id)
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('auditable_id', 'like', "%{$search}%")),
                TextColumn::make('alteracoes')
                    ->label('Campos alterados')
                    ->getStateUsing(function (Audit $record) {
                        $campos = array_keys(((array) $record->old_values) + ((array) $record->new_values));
                        return $campos ? implode(', ', $campos) : '—';
                    })
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('user_id')
                    ->label('Autor')
                    ->placeholder('Sistema')
                    ->formatStateUsing(function ($state) {
                        $nome = optional(\App\Models\User::find($state))->name;
                        return $nome ?: "Usuário #{$state}";
                    }),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Ação')
                    ->options([
                        'created'  => 'Criação',
                        'updated'  => 'Alteração',
                        'deleted'  => 'Exclusão',
                        'restored' => 'Restauração',
                    ]),
                SelectFilter::make('auditable_type')
                    ->label('Registro')
                    ->options([
                        'App\\Models\\Nota'         => 'Nota',
                        'App\\Models\\Resultado'    => 'Resultado',
                        'App\\Models\\GabaritoItem' => 'Item de gabarito',
                        'App\\Models\\Pagamento'    => 'Pagamento',
                        'App\\Models\\Recurso'      => 'Recurso',
                    ]),
                Filter::make('periodo')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('de')->label('De'),
                        \Filament\Forms\Components\DatePicker::make('ate')->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['de'] ?? null, fn (Builder $q, $d) => $q->whereDate('created_at', '>=', Carbon::parse($d)))
                            ->when($data['ate'] ?? null, fn (Builder $q, $d) => $q->whereDate('created_at', '<=', Carbon::parse($d)));
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detalhes')
                    ->modalHeading('Detalhe do registro de auditoria'),
            ])
            ->toolbarActions([]);
    }
}
