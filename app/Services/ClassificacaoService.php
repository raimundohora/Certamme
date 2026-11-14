<?php
namespace App\Services;

use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Nota;
use App\Models\Resultado;

class ClassificacaoService
{
    /**
     * Agrega as notas da etapa por candidato, exclui eliminados e classifica os demais.
     * Desempate (padrao): maior total -> mais acertos -> menor numero de inscricao.
     */
    public function classificar(Etapa $etapa): void
    {
        $porInsc = Nota::where('etapa_id', $etapa->id)->get()->groupBy('inscricao_id');
        $inscricoes = Inscricao::whereIn('id', $porInsc->keys()->all())->get()->keyBy('id');

        $linhas = [];
        foreach ($porInsc as $inscId => $grupo) {
            $linhas[] = [
                'inscId'    => (int) $inscId,
                'total'     => round($grupo->sum(fn ($n) => (float) $n->nota_final), 2),
                'acertos'   => (int) $grupo->sum(fn ($n) => $n->acertos),
                'eliminado' => $grupo->contains(fn ($n) => $n->eliminado),
                'numero'    => optional($inscricoes->get($inscId))->numero_inscricao ?? '',
            ];
        }

        $validos = array_values(array_filter($linhas, fn ($l) => ! $l['eliminado']));
        usort($validos, fn ($a, $b) =>
            [$b['total'], $b['acertos'], $a['numero']] <=> [$a['total'], $a['acertos'], $b['numero']]
        );
        $posicao = [];
        foreach ($validos as $i => $l) {
            $posicao[$l['inscId']] = $i + 1;
        }

        foreach ($linhas as $l) {
            Resultado::updateOrCreate(
                ['etapa_id' => $etapa->id, 'inscricao_id' => $l['inscId']],
                [
                    'total'         => $l['total'],
                    'acertos'       => $l['acertos'],
                    'eliminado'     => $l['eliminado'],
                    'classificacao' => $l['eliminado'] ? null : ($posicao[$l['inscId']] ?? null),
                    'calculado_em'  => now(),
                ],
            );
        }
    }
}
