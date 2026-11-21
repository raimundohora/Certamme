<?php

namespace App\Filament\Concerns;

use App\Support\ConcursoAtual;
use Illuminate\Database\Eloquent\Builder;

/**
 * Recurso Filament que vive DENTRO de um concurso. Enquanto houver concurso
 * selecionado, a listagem filtra por ele e o item aparece no menu; sem concurso,
 * o item some do menu (forca escolher um concurso primeiro, igual ao ProSeleta).
 * Requer que o model tenha a coluna concurso_id.
 */
trait ScopedToConcurso
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $cid = ConcursoAtual::id();
        if ($cid) {
            $query->where($query->getModel()->getTable() . '.concurso_id', $cid);
        }
        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ConcursoAtual::id() !== null;
    }
}
