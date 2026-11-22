<?php

namespace App\Services;

use App\Enums\StatusSubjetiva;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Nota;
use App\Models\ProvaSubjetiva;

/**
 * Prova discursiva/subjetiva (Fase 3): gera as folhas anônimas (uma por inscrição, com
 * codigo_controle) e consolida a dupla correção. Cada folha recebe N correções
 * independentes (CorrecaoSubjetiva); a consolidação calcula a nota final pela média das
 * duas correções mais próximas, marca DIVERGENTE quando a menor diferença passa do limite
 * (pede 3ª correção), e gera uma Nota por folha corrigida para a apuração ranquear.
 */
class DiscursivaService
{
    public const DISCIPLINA = 'Discursiva';

    /** Cria uma folha (ProvaSubjetiva) para cada inscrição confirmada da etapa que ainda não tem. */
    public function gerarFolhas(Etapa $etapa): array
    {
        $query = Inscricao::where('concurso_id', $etapa->concurso_id)
            ->where('status', 'confirmada');
        if ($etapa->cargo_id) {
            $query->where('cargo_id', $etapa->cargo_id);
        }
        $inscricoes = $query->get(['id', 'tenant_id']);

        $criadas = 0;
        $existentes = 0;
        foreach ($inscricoes as $insc) {
            $existe = ProvaSubjetiva::where('etapa_id', $etapa->id)
                ->where('inscricao_id', $insc->id)
                ->exists();
            if ($existe) {
                $existentes++;
                continue;
            }
            ProvaSubjetiva::create([
                'tenant_id'    => $insc->tenant_id ?? $etapa->tenant_id,
                'etapa_id'     => $etapa->id,
                'inscricao_id' => $insc->id,
                'status'       => StatusSubjetiva::PENDENTE->value,
            ]);
            $criadas++;
        }

        return ['criadas' => $criadas, 'existentes' => $existentes, 'total' => $criadas + $existentes];
    }

    /**
     * Consolida as correções da etapa. Nota final = média das duas correções mais próximas.
     * Menor diferença > $discrepanciaMax => DIVERGENTE (pede 3ª correção). < 2 correções => PENDENTE.
     * Folhas corrigidas geram/atualizam uma Nota (disciplina "Discursiva") para a apuração.
     */
    public function consolidar(Etapa $etapa, float $discrepanciaMax): array
    {
        $provas = ProvaSubjetiva::with('correcoes:id,prova_subjetiva_id,nota')
            ->where('etapa_id', $etapa->id)
            ->get();

        $corrigidas = 0;
        $divergentes = 0;
        $pendentes = 0;
        $notas = 0;

        foreach ($provas as $ps) {
            $valores = $ps->correcoes
                ->pluck('nota')
                ->filter(fn ($n) => $n !== null)
                ->map(fn ($n) => (float) $n)
                ->sort()
                ->values();

            if ($valores->count() < 2) {
                $ps->status = StatusSubjetiva::PENDENTE;
                $ps->nota_final = null;
                $ps->save();
                $pendentes++;
                continue;
            }

            // Par consecutivo de menor diferença (as duas correções mais próximas).
            $menorDiff = INF;
            $par = [$valores[0], $valores[1]];
            for ($i = 0; $i < $valores->count() - 1; $i++) {
                $d = abs($valores[$i + 1] - $valores[$i]);
                if ($d < $menorDiff) {
                    $menorDiff = $d;
                    $par = [$valores[$i], $valores[$i + 1]];
                }
            }

            if ($menorDiff <= $discrepanciaMax) {
                $notaFinal = round(($par[0] + $par[1]) / 2, 2);
                $ps->status = StatusSubjetiva::CORRIGIDA;
                $ps->nota_final = $notaFinal;
                $ps->save();
                $corrigidas++;

                Nota::updateOrCreate(
                    ['inscricao_id' => $ps->inscricao_id, 'etapa_id' => $etapa->id, 'disciplina' => self::DISCIPLINA],
                    [
                        'tenant_id'    => $ps->tenant_id ?? $etapa->tenant_id,
                        'nota_bruta'   => $notaFinal,
                        'nota_final'   => $notaFinal,
                        'acertos'      => 0,
                        'eliminado'    => false,
                        'calculada_em' => now(),
                    ],
                );
                $notas++;
            } else {
                $ps->status = StatusSubjetiva::DIVERGENTE;
                $ps->nota_final = null;
                $ps->save();
                $divergentes++;
            }
        }

        return [
            'corrigidas'  => $corrigidas,
            'divergentes' => $divergentes,
            'pendentes'   => $pendentes,
            'notas'       => $notas,
            'total'       => $provas->count(),
        ];
    }
}
