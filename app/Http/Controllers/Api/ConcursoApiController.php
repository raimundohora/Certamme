<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Resultado;
use App\Models\Tenant;
use Illuminate\Http\Request;

/**
 * API pública (por organizadora, via token bearer) — somente leitura.
 * Todos os modelos são filtrados pelo tenant do token (BelongsToTenant),
 * então um token nunca enxerga dados de outra organizadora.
 */
class ConcursoApiController extends Controller
{
    /** Identifica a organizadora dona do token. */
    public function me()
    {
        $t = Tenant::current();

        return response()->json([
            'organizadora' => ['slug' => $t?->slug, 'nome' => $t?->nome],
        ]);
    }

    public function index()
    {
        $concursos = Concurso::query()
            ->withCount(['cargos'])
            ->orderByDesc('ano')
            ->get()
            ->map(fn (Concurso $c) => [
                'id'          => $c->id,
                'nome'        => $c->nome,
                'ano'         => $c->ano,
                'situacao'    => $c->situacao,
                'cargos'      => $c->cargos_count,
                'inscritos'   => Inscricao::where('concurso_id', $c->id)->count(),
            ]);

        return response()->json(['data' => $concursos]);
    }

    public function show(int $concurso)
    {
        $c = Concurso::findOrFail($concurso);

        $etapas = Etapa::where('concurso_id', $c->id)->orderBy('ordem')->get()
            ->map(fn (Etapa $e) => [
                'ordem'          => $e->ordem,
                'tipo'           => $e->tipo?->value,
                'eliminatoria'   => (bool) $e->eliminatoria,
                'classificatoria' => (bool) $e->classificatoria,
            ]);

        return response()->json(['data' => [
            'id'       => $c->id,
            'nome'     => $c->nome,
            'ano'      => $c->ano,
            'situacao' => $c->situacao,
            'cargos'   => $c->cargos()->get(['id', 'nome', 'vagas', 'taxa_inscricao'])
                ->map(fn ($cargo) => [
                    'id'    => $cargo->id,
                    'nome'  => $cargo->nome,
                    'vagas' => $cargo->vagas,
                    'taxa'  => $cargo->taxa_inscricao,
                ]),
            'etapas'     => $etapas,
            'cronograma' => $c->cronogramas()->orderBy('data')->get(['evento', 'data'])
                ->map(fn ($cr) => ['evento' => $cr->evento, 'data' => optional($cr->data)->toDateString()]),
        ]]);
    }

    public function inscritos(int $concurso, Request $request)
    {
        $c = Concurso::findOrFail($concurso);

        $inscritos = Inscricao::where('concurso_id', $c->id)
            ->with('cargo:id,nome')
            ->orderBy('numero_inscricao')
            ->paginate(min((int) $request->integer('por_pagina', 100), 200))
            ->through(fn (Inscricao $i) => [
                'numero_inscricao' => $i->numero_inscricao,
                'nome'             => $i->nome,
                'cargo'            => $i->cargo?->nome,
                'modalidade'       => $i->modalidade?->value,
                'status'           => $i->status?->value,
            ]);

        return response()->json($inscritos);
    }

    public function resultados(int $concurso, Request $request)
    {
        $c = Concurso::findOrFail($concurso);

        $query = Resultado::whereHas('etapa', fn ($q) => $q->where('concurso_id', $c->id))
            ->with(['inscricao:id,numero_inscricao,nome,cargo_id', 'inscricao.cargo:id,nome', 'etapa:id,ordem']);

        if ($request->filled('etapa')) {
            $query->whereHas('etapa', fn ($q) => $q->where('ordem', $request->integer('etapa')));
        }

        $resultados = $query
            ->orderByRaw('classificacao is null, classificacao')
            ->paginate(min((int) $request->integer('por_pagina', 100), 200))
            ->through(fn (Resultado $r) => [
                'numero_inscricao'   => $r->inscricao?->numero_inscricao,
                'nome'               => $r->inscricao?->nome,
                'cargo'              => $r->inscricao?->cargo?->nome,
                'etapa'              => $r->etapa?->ordem,
                'total'              => $r->total,
                'acertos'            => $r->acertos,
                'classificacao'      => $r->classificacao,
                'classificacao_cota' => $r->classificacao_cota,
                'situacao'           => $r->situacao,
                'eliminado'          => (bool) $r->eliminado,
            ]);

        return response()->json($resultados);
    }
}
