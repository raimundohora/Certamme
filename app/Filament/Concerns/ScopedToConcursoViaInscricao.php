<?php

namespace App\Filament\Concerns;

use App\Support\ConcursoAtual;
use Illuminate\Database\Eloquent\Builder;

/**
 * Recurso que vive dentro de um concurso via a inscricao (nao tem concurso_id direto).
 * Filtra por inscricao.concurso_id enquanto houver concurso selecionado e some do menu sem concurso.
 */
trait ScopedToConcursoViaInscricao
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $cid = ConcursoAtual::id();
        if ($cid) {
            $query->whereHas('inscricao', fn (Builder $q) => $q->where('concurso_id', $cid));
        }
        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ConcursoAtual::id() !== null;
    }
}
