<?php

namespace App\Services;

use App\Enums\StatusInscricao;
use App\Models\Alocacao;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\LocalProva;

/**
 * Ensalamento (Fase 2): distribui as inscricoes de uma etapa nas salas do concurso,
 * respeitando a capacidade de cada sala e numerando a carteira (1..capacidade) por sala.
 * Idempotente: apaga as alocacoes anteriores da etapa e refaz.
 *
 * Opcoes:
 *   ordem             -> 'nome' (padrao) ou 'numero_inscricao'
 *   incluir_pendentes -> inclui inscricoes pendentes (padrao: so confirmadas)
 */
class EnsalamentoService
{
    /** @return array{alocados:int,sem_vaga:int,salas_usadas:int,capacidade_total:int,candidatos:int} */
    public function ensalar(Etapa $etapa, array $opcoes = []): array
    {
        $ordem = $opcoes['ordem'] ?? 'nome';
        $incluirPendentes = (bool) ($opcoes['incluir_pendentes'] ?? false);

        // Salas do concurso (por local), em ordem estavel
        $locais = LocalProva::with(['salas' => fn ($q) => $q->orderBy('nome')])
            ->where('concurso_id', $etapa->concurso_id)
            ->orderBy('nome')
            ->get();

        $salas = [];
        foreach ($locais as $local) {
            foreach ($local->salas as $sala) {
                if ((int) $sala->capacidade > 0) {
                    $salas[] = $sala;
                }
            }
        }
        $capacidadeTotal = array_sum(array_map(fn ($s) => (int) $s->capacidade, $salas));

        // Inscricoes a alocar
        $status = $incluirPendentes
            ? [StatusInscricao::CONFIRMADA->value, StatusInscricao::PENDENTE->value]
            : [StatusInscricao::CONFIRMADA->value];

        $q = Inscricao::where('concurso_id', $etapa->concurso_id)->whereIn('status', $status);
        if ($etapa->cargo_id) {
            $q->where('cargo_id', $etapa->cargo_id);
        }
        $q = $ordem === 'numero_inscricao'
            ? $q->orderBy('numero_inscricao')
            : $q->orderBy('nome')->orderBy('numero_inscricao');
        $inscricoes = $q->get(['id', 'nome', 'numero_inscricao']);

        // Refaz do zero
        Alocacao::where('etapa_id', $etapa->id)->delete();

        $alocados = 0;
        $salasUsadas = 0;
        $idxSala = 0;
        $carteira = 0;
        $novos = [];
        $agora = now();

        foreach ($inscricoes as $insc) {
            // avanca para a proxima sala com vaga
            while ($idxSala < count($salas) && $carteira >= (int) $salas[$idxSala]->capacidade) {
                $idxSala++;
                $carteira = 0;
            }
            if ($idxSala >= count($salas)) {
                break; // acabaram as vagas
            }
            if ($carteira === 0) {
                $salasUsadas++;
            }
            $carteira++;
            $novos[] = [
                'tenant_id'    => $etapa->tenant_id,
                'etapa_id'     => $etapa->id,
                'inscricao_id' => $insc->id,
                'sala_id'      => $salas[$idxSala]->id,
                'carteira'     => $carteira,
                'created_at'   => $agora,
                'updated_at'   => $agora,
            ];
            $alocados++;
        }

        if ($novos) {
            foreach (array_chunk($novos, 500) as $chunk) {
                Alocacao::insert($chunk);
            }
        }

        return [
            'alocados'         => $alocados,
            'sem_vaga'         => max(0, $inscricoes->count() - $alocados),
            'salas_usadas'     => $salasUsadas,
            'capacidade_total' => $capacidadeTotal,
            'candidatos'       => $inscricoes->count(),
        ];
    }
}
