<?php

namespace App\Services;

use App\Models\Etapa;
use App\Models\Nota;
use App\Models\Resultado;

/**
 * Motor de apuracao de uma Etapa (Fase 2): soma as notas por inscricao (respeitando
 * override x peso), aplica eliminacao (nota minima / nota eliminada), ranqueia os
 * nao-eliminados por total desc gravando a CLASSIFICACAO GERAL e a CLASSIFICACAO POR
 * COTA (posicao dentro da modalidade da inscricao). Idempotente (updateOrCreate).
 */
class ApuracaoService
{
    public function apurarEtapa(Etapa $etapa): array
    {
        $peso = (float) ($etapa->peso ?? 1) ?: 1.0;
        $notasPorInscricao = Nota::where('etapa_id', $etapa->id)->get()->groupBy('inscricao_id');

        $apurados = 0;
        $eliminados = 0;

        foreach ($notasPorInscricao as $inscricaoId => $notas) {
            $total = 0.0;
            $acertos = 0;
            $elimPorNota = false;
            foreach ($notas as $n) {
                $valor = $n->override ? (float) $n->nota_override : (float) ($n->nota_final ?? $n->nota_bruta ?? 0);
                $total += $valor;
                $acertos += (int) $n->acertos;
                if ($n->eliminado) {
                    $elimPorNota = true;
                }
            }
            $total = round($total * $peso, 2);

            $eliminado = $elimPorNota
                || ($etapa->eliminatoria && $etapa->nota_minima !== null && $total < (float) $etapa->nota_minima);

            Resultado::updateOrCreate(
                ['etapa_id' => $etapa->id, 'inscricao_id' => $inscricaoId],
                [
                    'tenant_id'          => $etapa->tenant_id,
                    'total'              => $total,
                    'acertos'            => $acertos,
                    'eliminado'          => $eliminado,
                    'classificacao'      => null,
                    'classificacao_cota' => null,
                    'calculado_em'       => now(),
                ],
            );
            $apurados++;
            if ($eliminado) {
                $eliminados++;
            }
        }

        // Nao-eliminados, com a modalidade da inscricao, ordenados por total desc (desempate por inscricao)
        $ranking = Resultado::with('inscricao:id,modalidade')
            ->where('etapa_id', $etapa->id)
            ->where('eliminado', false)
            ->orderByDesc('total')
            ->orderBy('inscricao_id')
            ->get();

        $posGeral = 0;
        $posCota = [];
        foreach ($ranking as $r) {
            $posGeral++;
            $r->classificacao = $posGeral;

            $mod = $r->inscricao?->modalidade?->value ?? 'ampla';
            $posCota[$mod] = ($posCota[$mod] ?? 0) + 1;
            $r->classificacao_cota = $posCota[$mod];

            $r->save();
        }

        return [
            'apurados'      => $apurados,
            'eliminados'    => $eliminados,
            'classificados' => $posGeral,
            'por_cota'      => $posCota,
        ];
    }
}
