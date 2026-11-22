<?php

namespace App\Services;

use App\Models\Etapa;
use App\Models\Nota;
use App\Models\Titulo;
use App\Models\TituloTipo;

/**
 * Prova de Títulos (Fase 3): converte os títulos avaliados em uma Nota por inscrição
 * para a etapa de títulos, respeitando o teto de pontuação de cada tipo
 * (TituloTipo::pontuacao_maxima). Depois disso, a apuração normal (ApuracaoService)
 * ranqueia os candidatos — títulos entram no pipeline como qualquer outra etapa.
 *
 * Só contam títulos já avaliados/conferidos (status != pendente). Idempotente.
 */
class TitulosService
{
    public const DISCIPLINA = 'Títulos';

    public function gerarNotas(Etapa $etapa): array
    {
        $tipos = TituloTipo::where('etapa_id', $etapa->id)->get()->keyBy('id');

        if ($tipos->isEmpty()) {
            return ['tipos' => 0, 'inscricoes' => 0, 'titulos' => 0, 'sem_tipos' => true];
        }

        $titulosPorInscricao = Titulo::whereIn('titulo_tipo_id', $tipos->keys())
            ->where('status', '!=', 'pendente')
            ->get()
            ->groupBy('inscricao_id');

        $inscricoes = 0;
        $totalTitulos = 0;

        foreach ($titulosPorInscricao as $inscricaoId => $lista) {
            // Soma por tipo, aplicando o teto de cada tipo.
            $porTipo = [];
            foreach ($lista as $t) {
                $porTipo[$t->titulo_tipo_id] = ($porTipo[$t->titulo_tipo_id] ?? 0) + (float) ($t->pontos ?? 0);
                $totalTitulos++;
            }

            $total = 0.0;
            foreach ($porTipo as $tipoId => $soma) {
                $max = (float) ($tipos[$tipoId]->pontuacao_maxima ?? 0);
                $total += ($max > 0) ? min($soma, $max) : $soma;
            }
            $total = round($total, 2);

            Nota::updateOrCreate(
                ['inscricao_id' => $inscricaoId, 'etapa_id' => $etapa->id, 'disciplina' => self::DISCIPLINA],
                [
                    'tenant_id'    => $etapa->tenant_id,
                    'nota_bruta'   => $total,
                    'nota_final'   => $total,
                    'acertos'      => 0,
                    'eliminado'    => false,
                    'calculada_em' => now(),
                ],
            );
            $inscricoes++;
        }

        return [
            'tipos'      => $tipos->count(),
            'inscricoes' => $inscricoes,
            'titulos'    => $totalTitulos,
            'sem_tipos'  => false,
        ];
    }
}
