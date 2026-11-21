<?php

namespace App\Services;

use App\Models\Cargo;
use App\Models\Etapa;
use App\Models\Nota;
use App\Models\Resultado;

/**
 * Motor de apuracao de uma Etapa (Fase 2): soma as notas por inscricao (respeitando
 * override x peso), aplica eliminacao (nota minima / nota eliminada), ranqueia os
 * nao-eliminados por total desc gravando a CLASSIFICACAO GERAL e a CLASSIFICACAO POR
 * COTA (posicao dentro da modalidade da inscricao). Em seguida aplica o CORTE POR VAGAS
 * (por cargo + modalidade): os primeiros N de cada modalidade ficam "aprovado", o resto
 * "classificavel". Idempotente (updateOrCreate).
 *
 * Observacao: o corte considera a cota declarada pelo candidato (sem alternancia
 * ampla x cota entre as listas) — refinamento previsto para fase posterior.
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
                    'situacao'           => $eliminado ? 'eliminado' : null,
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

        // Nao-eliminados, com a modalidade/cargo da inscricao, ordenados por total desc (desempate por inscricao)
        $ranking = Resultado::with('inscricao:id,modalidade,cargo_id')
            ->where('etapa_id', $etapa->id)
            ->where('eliminado', false)
            ->orderByDesc('total')
            ->orderBy('inscricao_id')
            ->get();

        // Cargos envolvidos (para o corte por vagas)
        $cargoIds = $ranking->pluck('inscricao.cargo_id')->filter();
        if ($etapa->cargo_id) {
            $cargoIds->push($etapa->cargo_id);
        }
        $cargos = Cargo::whereIn('id', $cargoIds->unique()->values())->get()->keyBy('id');

        $posGeral = 0;
        $posCota = [];   // modalidade => contador (classificacao_cota, no ambito da etapa)
        $posVaga = [];   // "cargoId:modalidade" => contador (para o corte por vagas)
        $aprovados = 0;

        foreach ($ranking as $r) {
            $posGeral++;
            $r->classificacao = $posGeral;

            $mod = $r->inscricao?->modalidade?->value ?? 'ampla';
            $posCota[$mod] = ($posCota[$mod] ?? 0) + 1;
            $r->classificacao_cota = $posCota[$mod];

            // Corte por vagas (por cargo + modalidade)
            $cargoId = $etapa->cargo_id ?: $r->inscricao?->cargo_id;
            $cargo = $cargoId ? $cargos->get($cargoId) : null;

            if (! $cargo) {
                $r->situacao = 'classificavel'; // sem cargo/vagas conhecidos: so classifica
            } else {
                $vagas = $cargo->vagasPara($mod);
                $kv = $cargoId . ':' . $mod;
                $posVaga[$kv] = ($posVaga[$kv] ?? 0) + 1;
                if ($posVaga[$kv] <= $vagas) {
                    $r->situacao = 'aprovado';
                    $aprovados++;
                } else {
                    $r->situacao = 'classificavel';
                }
            }

            $r->save();
        }

        return [
            'apurados'      => $apurados,
            'eliminados'    => $eliminados,
            'classificados' => $posGeral,
            'aprovados'     => $aprovados,
            'por_cota'      => $posCota,
        ];
    }
}
