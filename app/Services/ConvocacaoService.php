<?php

namespace App\Services;

use App\Models\Convocacao;
use App\Models\Etapa;
use App\Models\Resultado;

/**
 * Convocacao (Fase 2): gera convocacoes a partir do Resultado de uma etapa — os candidatos
 * aprovados (ou aprovados + classificaveis) sao convocados (para a proxima fase). Idempotente:
 * refaz as convocacoes da etapa. As convocacoes nascem nao publicadas (o admin publica depois).
 *
 * Opcoes:
 *   criterio -> 'aprovado' (padrao) ou 'classificavel' (inclui tambem os classificaveis)
 */
class ConvocacaoService
{
    /** @return array{convocados:int,criterio:string} */
    public function convocar(Etapa $etapa, array $opcoes = []): array
    {
        $criterio = $opcoes['criterio'] ?? 'aprovado';
        $situacoes = $criterio === 'classificavel'
            ? ['aprovado', 'classificavel']
            : ['aprovado'];

        $resultados = Resultado::where('etapa_id', $etapa->id)
            ->where('eliminado', false)
            ->whereIn('situacao', $situacoes)
            ->orderBy('classificacao')
            ->get(['inscricao_id', 'classificacao', 'situacao']);

        // Refaz do zero (idempotente)
        Convocacao::where('etapa_id', $etapa->id)->delete();

        $agora = now();
        $rows = [];
        foreach ($resultados as $r) {
            $rows[] = [
                'tenant_id'    => $etapa->tenant_id,
                'etapa_id'     => $etapa->id,
                'inscricao_id' => $r->inscricao_id,
                'publicado'    => false,
                'observacao'   => sprintf(
                    'Convocado(a) pela etapa %s — %sº classificado(a) (%s).',
                    $etapa->ordem,
                    $r->classificacao ?? '—',
                    $r->situacao,
                ),
                'created_at'   => $agora,
                'updated_at'   => $agora,
            ];
        }

        if ($rows) {
            foreach (array_chunk($rows, 500) as $chunk) {
                Convocacao::insert($chunk);
            }
        }

        return ['convocados' => count($rows), 'criterio' => $criterio];
    }
}
