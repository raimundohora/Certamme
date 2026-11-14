<?php
namespace App\Services;

use App\Enums\SituacaoQuestao;
use App\Models\GabaritoItem;
use App\Models\LeituraCartao;
use App\Models\Nota;
use App\Models\Prova;

class CalcularNotaService
{
    /**
     * Cruza as leituras da prova com o gabarito e grava uma Nota por (inscricao, etapa, disciplina).
     * Regras: ANULADA = ponto p/ todos; ALTERADA = usa resposta_nova; nota_minima da etapa elimina.
     * Notas com override=true (ajuste manual) sao PRESERVADAS (nao recalcula por cima).
     */
    public function calcular(Prova $prova): void
    {
        $prova->loadMissing('etapa');
        $etapa = $prova->etapa;
        $notaMinima = $etapa->nota_minima !== null ? (float) $etapa->nota_minima : null;

        $gabarito = GabaritoItem::where('prova_id', $prova->id)->get()->keyBy('numero');
        $leituras = LeituraCartao::where('prova_id', $prova->id)->get();

        foreach ($leituras as $leitura) {
            $porDisciplina = [];
            foreach ($gabarito as $numero => $item) {
                $disc = $item->disciplina ?: 'Geral';
                $porDisciplina[$disc] ??= ['acertos' => 0, 'nota' => 0.0];

                if ($item->situacao === SituacaoQuestao::ANULADA) {
                    $acertou = true;
                } else {
                    $marcada = strtoupper((string) ($leitura->respostas[(string) $numero] ?? ''));
                    $correta = strtoupper((string) $item->respostaEfetiva());
                    $acertou = $marcada !== '' && $marcada === $correta;
                }

                if ($acertou) {
                    $porDisciplina[$disc]['acertos']++;
                    $porDisciplina[$disc]['nota'] += (float) $item->peso;
                }
            }

            $total = array_sum(array_column($porDisciplina, 'nota'));
            $eliminado = $notaMinima !== null && $total < $notaMinima;

            foreach ($porDisciplina as $disc => $v) {
                $existente = Nota::where('inscricao_id', $leitura->inscricao_id)
                    ->where('etapa_id', $etapa->id)->where('disciplina', $disc)->first();

                if ($existente && $existente->override) {
                    continue; // ajuste manual trava o recalculo
                }

                Nota::updateOrCreate(
                    ['inscricao_id' => $leitura->inscricao_id, 'etapa_id' => $etapa->id, 'disciplina' => $disc],
                    [
                        'acertos'      => $v['acertos'],
                        'nota_bruta'   => $v['nota'],
                        'nota_final'   => $v['nota'],
                        'eliminado'    => $eliminado,
                        'calculada_em' => now(),
                    ],
                );
            }
        }
    }
}
