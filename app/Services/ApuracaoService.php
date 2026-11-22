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
 * A modalidade usada na classificacao por cota e no corte e a EFETIVA (Inscricao::
 * modalidadeEfetiva): se a cota foi indeferida na heteroidentificacao, o candidato
 * concorre na ampla.
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

        // Nao-eliminados, ordenados por total desc e depois pelos criterios de desempate configurados.
        $criterios = $etapa->config['desempate'] ?? ['maior_idade', 'mais_acertos', 'numero_inscricao'];
        $ranking = Resultado::with([
                'inscricao:id,numero_inscricao,modalidade,cargo_id,candidato_id',
                'inscricao.candidato:id,data_nascimento',
                'inscricao.heteroidentificacao:id,inscricao_id,status',
            ])
            ->where('etapa_id', $etapa->id)
            ->where('eliminado', false)
            ->get()
            ->sort(function (Resultado $a, Resultado $b) use ($criterios) {
                $c = (float) $b->total <=> (float) $a->total; // nota desc (total e string por causa do cast decimal)
                if ($c !== 0) {
                    return $c;
                }
                foreach ($criterios as $criterio) {
                    $c = $this->comparaDesempate($criterio, $a, $b);
                    if ($c !== 0) {
                        return $c;
                    }
                }
                return $a->inscricao_id <=> $b->inscricao_id; // fallback deterministico
            })
            ->values();

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

            $mod = $r->inscricao?->modalidadeEfetiva() ?? 'ampla';
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

    /**
     * Compara dois resultados por um criterio de desempate. Retorna <0 se $a vem antes.
     */
    private function comparaDesempate(string $criterio, Resultado $a, Resultado $b): int
    {
        switch ($criterio) {
            case 'maior_idade':
                $da = $a->inscricao?->candidato?->data_nascimento;
                $db = $b->inscricao?->candidato?->data_nascimento;
                if ($da && $db) {
                    return $da <=> $db; // mais velho (data menor) primeiro
                }
                if ($da) {
                    return -1;
                }
                if ($db) {
                    return 1;
                }
                return 0;

            case 'mais_acertos':
                return (int) $b->acertos <=> (int) $a->acertos; // mais acertos primeiro

            case 'numero_inscricao':
                $na = (int) ltrim((string) $a->inscricao?->numero_inscricao, '0');
                $nb = (int) ltrim((string) $b->inscricao?->numero_inscricao, '0');
                return $na <=> $nb; // menor numero primeiro

            default:
                return 0;
        }
    }
}
