<?php
namespace App\Services;

use App\Enums\StatusInscricao;
use App\Models\Alocacao;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Sala;

class EnsalamentoService
{
    /**
     * Distribui os inscritos CONFIRMADOS do concurso nas salas (ordem alfabetica),
     * respeitando a capacidade. Gera o "mapa de sala" (Alocacao: sala + carteira).
     * Re-roda limpo (apaga alocacoes anteriores da etapa).
     */
    public function alocar(Etapa $etapa): int
    {
        $etapa->loadMissing('concurso');
        $concursoId = $etapa->concurso_id;

        Alocacao::where('etapa_id', $etapa->id)->delete();

        $salas = Sala::whereHas('local', fn ($q) => $q->where('concurso_id', $concursoId))
            ->orderBy('local_prova_id')->orderBy('id')->get();

        $inscritos = Inscricao::where('concurso_id', $concursoId)
            ->where('status', StatusInscricao::CONFIRMADA)
            ->orderBy('nome')->get();

        $salaIdx = 0;
        $carteira = 0;
        $alocados = 0;

        foreach ($inscritos as $insc) {
            while ($salaIdx < $salas->count() && $carteira >= $salas[$salaIdx]->capacidade) {
                $salaIdx++;
                $carteira = 0;
            }
            if ($salaIdx >= $salas->count()) {
                break; // acabaram as vagas (faltou sala)
            }
            $carteira++;
            Alocacao::create([
                'etapa_id'     => $etapa->id,
                'inscricao_id' => $insc->id,
                'sala_id'      => $salas[$salaIdx]->id,
                'carteira'     => $carteira,
            ]);
            $alocados++;
        }

        return $alocados;
    }
}
