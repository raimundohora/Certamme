<?php

namespace App\Filament\Resources\CorrecaoSubjetivas\Schemas;

use App\Models\ProvaSubjetiva;
use App\Support\ConcursoAtual;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CorrecaoSubjetivaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('prova_subjetiva_id')
                    ->label('Folha (código de controle)')
                    ->options(function () {
                        $cid = ConcursoAtual::id();
                        return ProvaSubjetiva::query()
                            ->when($cid, fn (Builder $q) => $q->whereHas('inscricao', fn (Builder $x) => $x->where('concurso_id', $cid)))
                            ->orderBy('codigo_controle')
                            ->pluck('codigo_controle', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->helperText('Identifique a folha pelo código. O candidato não é exibido (correção sigilosa).'),
                TextInput::make('corretor')
                    ->label('Corretor(a)')
                    ->default(fn () => auth()->user()?->name)
                    ->required(),
                TextInput::make('nota')
                    ->label('Nota atribuída')
                    ->numeric()
                    ->step('0.01')
                    ->minValue(0)
                    ->required(),
                KeyValue::make('quesitos')
                    ->label('Quesitos (critério → pontos)')
                    ->keyLabel('Critério')
                    ->valueLabel('Pontos')
                    ->addActionLabel('Adicionar critério')
                    ->columnSpanFull(),
            ]);
    }
}
