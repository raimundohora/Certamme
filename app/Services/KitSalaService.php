<?php

namespace App\Services;

use App\Models\Alocacao;
use App\Models\Etapa;

/**
 * Kit de sala (Fase 2): monta os dados dos documentos de sala a partir do ensalamento.
 * Por ora: lista de presenca (um bloco por sala, candidatos por carteira).
 */
class KitSalaService
{
    public function dadosListaPresenca(Etapa $etapa): array
    {
        $alocacoes = Alocacao::with(['sala.local', 'inscricao:id,nome,numero_inscricao'])
            ->where('etapa_id', $etapa->id)
            ->get();

        $salas = $alocacoes
            ->groupBy('sala_id')
            ->map(function ($grupo) {
                $sala = $grupo->first()->sala;
                return [
                    'local'   => $sala?->local?->nome ?? '—',
                    'sala'    => $sala?->nome ?? '—',
                    'ordem'   => ($sala?->local?->nome ?? '') . '|' . ($sala?->nome ?? ''),
                    'linhas'  => $grupo->sortBy('carteira')->map(fn ($a) => [
                        'carteira' => $a->carteira,
                        'numero'   => $a->inscricao?->numero_inscricao ?? '—',
                        'nome'     => $a->inscricao?->nome ?? '—',
                    ])->values()->all(),
                ];
            })
            ->sortBy('ordem')
            ->values()
            ->all();

        $etapa->loadMissing(['concurso:id,nome', 'cargo:id,nome']);

        return [
            'concurso'   => $etapa->concurso?->nome ?? 'Concurso',
            'cargo'      => $etapa->cargo?->nome,
            'etapaOrdem' => $etapa->ordem,
            'geradoEm'   => now()->format('d/m/Y H:i'),
            'salas'      => $salas,
        ];
    }
}
