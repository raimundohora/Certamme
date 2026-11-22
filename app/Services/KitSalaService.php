<?php

namespace App\Services;

use App\Models\Alocacao;
use App\Models\Etapa;
use App\Models\Prova;
use Illuminate\Support\Collection;

/**
 * Kit de sala (Fase 2): monta os dados dos documentos de sala a partir do ensalamento.
 * Documentos: lista de presença, mapa de sala, ata de sala e cartões-resposta.
 * Todos partem do mesmo agrupamento por sala (helper `blocos`) + cabeçalho comum.
 */
class KitSalaService
{
    /** Agrupa as alocações da etapa por sala, já ordenadas e com os candidatos por carteira. */
    private function blocos(Etapa $etapa): Collection
    {
        return Alocacao::with(['sala.local', 'inscricao:id,nome,numero_inscricao'])
            ->where('etapa_id', $etapa->id)
            ->get()
            ->groupBy('sala_id')
            ->map(function ($grupo) {
                $sala = $grupo->first()->sala;
                $linhas = $grupo->sortBy('carteira')->map(fn ($a) => [
                    'carteira' => $a->carteira,
                    'numero'   => $a->inscricao?->numero_inscricao ?? '—',
                    'nome'     => $a->inscricao?->nome ?? '—',
                ])->values();

                return [
                    'local'      => $sala?->local?->nome ?? '—',
                    'endereco'   => $sala?->local?->endereco ?? null,
                    'sala'       => $sala?->nome ?? '—',
                    'capacidade' => (int) ($sala?->capacidade ?? 0),
                    'ordem'      => ($sala?->local?->nome ?? '') . '|' . ($sala?->nome ?? ''),
                    'linhas'     => $linhas->all(),
                    'total'      => $linhas->count(),
                ];
            })
            ->sortBy('ordem')
            ->values();
    }

    /** Cabeçalho comum (concurso, cargo, etapa, data de geração). */
    private function cabecalho(Etapa $etapa): array
    {
        $etapa->loadMissing(['concurso:id,nome', 'cargo:id,nome']);

        return [
            'concurso'   => $etapa->concurso?->nome ?? 'Concurso',
            'cargo'      => $etapa->cargo?->nome,
            'etapaOrdem' => $etapa->ordem,
            'geradoEm'   => now()->format('d/m/Y H:i'),
        ];
    }

    public function dadosListaPresenca(Etapa $etapa): array
    {
        return $this->cabecalho($etapa) + ['salas' => $this->blocos($etapa)->all()];
    }

    public function dadosMapaSala(Etapa $etapa): array
    {
        $colunas = 5;

        $salas = $this->blocos($etapa)->map(function (array $b) use ($colunas) {
            // Quebra os assentos em "fileiras" de $colunas para desenhar a grade.
            $b['fileiras'] = array_chunk($b['linhas'], $colunas);
            $b['colunas']  = $colunas;
            return $b;
        })->all();

        return $this->cabecalho($etapa) + ['salas' => $salas];
    }

    public function dadosAta(Etapa $etapa): array
    {
        return $this->cabecalho($etapa) + ['salas' => $this->blocos($etapa)->all()];
    }

    /**
     * Cartões-resposta: um por candidato, com grade de bolhas.
     * @param int|null $qtdQuestoes se null, usa a maior qtd_questoes das provas da etapa.
     * @param int $alternativas número de alternativas por questão (A, B, C...).
     */
    public function dadosCartoes(Etapa $etapa, ?int $qtdQuestoes = null, int $alternativas = 5): array
    {
        $qtd = $qtdQuestoes ?? (int) Prova::where('etapa_id', $etapa->id)->max('qtd_questoes');
        $qtd = max(0, $qtd);
        $alternativas = max(2, min(6, $alternativas));

        $letras = array_slice(['A', 'B', 'C', 'D', 'E', 'F'], 0, $alternativas);

        // Lista plana de candidatos (um cartão por candidato), na ordem sala → carteira.
        $candidatos = [];
        foreach ($this->blocos($etapa) as $b) {
            foreach ($b['linhas'] as $l) {
                $candidatos[] = [
                    'local'    => $b['local'],
                    'sala'     => $b['sala'],
                    'carteira' => $l['carteira'],
                    'numero'   => $l['numero'],
                    'nome'     => $l['nome'],
                ];
            }
        }

        return $this->cabecalho($etapa) + [
            'qtdQuestoes'  => $qtd,
            'alternativas' => $alternativas,
            'letras'       => $letras,
            'candidatos'   => $candidatos,
        ];
    }
}
