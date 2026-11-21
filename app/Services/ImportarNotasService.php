<?php

namespace App\Services;

use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Nota;

/**
 * Importa notas de uma Etapa a partir de um CSV (planilha da leitora optica ou digitacao).
 *
 * Formato do CSV (cabecalho obrigatorio, ordem livre, separador "," ou ";"):
 *   numero_inscricao  -> OBRIGATORIO (casa com a inscricao do concurso da etapa)
 *   disciplina        -> opcional (vazio = nota geral da etapa)
 *   acertos           -> opcional (inteiro)
 *   nota              -> opcional (decimal; aceita virgula). Se vazia, usa acertos.
 *
 * Pelo menos uma de {acertos, nota} deve vir preenchida na linha.
 * Idempotente: regrava a mesma (inscricao, etapa, disciplina) via updateOrCreate.
 */
class ImportarNotasService
{
    /** @return array{criadas:int,atualizadas:int,ignoradas:int,erros:array<int,string>,linhas:int} */
    public function importar(Etapa $etapa, string $conteudo, ?string $disciplinaPadrao = null): array
    {
        $criadas = $atualizadas = $ignoradas = $linhas = 0;
        $erros = [];

        // Normaliza quebras de linha e BOM
        $conteudo = preg_replace('/^\xEF\xBB\xBF/', '', $conteudo);
        $raw = preg_split('/\r\n|\r|\n/', trim($conteudo));
        $raw = array_values(array_filter($raw, fn ($l) => trim($l) !== ''));

        if (count($raw) < 2) {
            return ['criadas' => 0, 'atualizadas' => 0, 'ignoradas' => 0, 'linhas' => 0,
                'erros' => ['CSV vazio ou sem linhas de dados (precisa de cabecalho + 1 linha).']];
        }

        $delim = (substr_count($raw[0], ';') >= substr_count($raw[0], ',')) ? ';' : ',';

        $header = array_map(fn ($h) => $this->slug($h), str_getcsv($raw[0], $delim, '"', ''));
        $idx = [
            'numero'     => $this->findCol($header, ['numero_inscricao', 'numero', 'inscricao', 'num_inscricao']),
            'disciplina' => $this->findCol($header, ['disciplina', 'materia', 'prova']),
            'acertos'    => $this->findCol($header, ['acertos', 'acerto', 'questoes']),
            'nota'       => $this->findCol($header, ['nota', 'nota_final', 'pontos', 'pontuacao']),
        ];

        if ($idx['numero'] === null) {
            return ['criadas' => 0, 'atualizadas' => 0, 'ignoradas' => 0, 'linhas' => 0,
                'erros' => ['Cabecalho precisa ter a coluna "numero_inscricao".']];
        }
        if ($idx['acertos'] === null && $idx['nota'] === null) {
            return ['criadas' => 0, 'atualizadas' => 0, 'ignoradas' => 0, 'linhas' => 0,
                'erros' => ['Cabecalho precisa ter ao menos uma coluna: "acertos" ou "nota".']];
        }

        // Mapa numero_inscricao -> inscricao (escopo do concurso e, se houver, do cargo da etapa)
        $q = Inscricao::query()->where('concurso_id', $etapa->concurso_id);
        if ($etapa->cargo_id) {
            $q->where('cargo_id', $etapa->cargo_id);
        }
        $mapa = $q->get(['id', 'numero_inscricao'])
            ->keyBy(fn ($i) => $this->normNumero($i->numero_inscricao));

        for ($i = 1; $i < count($raw); $i++) {
            $linhas++;
            $linhaNum = $i + 1;
            $cols = str_getcsv($raw[$i], $delim, '"', '');

            $numero = $this->normNumero($cols[$idx['numero']] ?? '');
            if ($numero === '') {
                $erros[] = "Linha {$linhaNum}: numero_inscricao vazio.";
                $ignoradas++;
                continue;
            }
            $insc = $mapa->get($numero);
            if (! $insc) {
                $erros[] = "Linha {$linhaNum}: inscricao {$numero} nao encontrada neste concurso/cargo.";
                $ignoradas++;
                continue;
            }

            $disciplina = $idx['disciplina'] !== null ? trim($cols[$idx['disciplina']] ?? '') : '';
            $disciplina = $disciplina !== '' ? $disciplina : ($disciplinaPadrao ?: null);

            $acertosRaw = $idx['acertos'] !== null ? trim($cols[$idx['acertos']] ?? '') : '';
            $notaRaw    = $idx['nota'] !== null ? trim($cols[$idx['nota']] ?? '') : '';

            if ($acertosRaw === '' && $notaRaw === '') {
                $erros[] = "Linha {$linhaNum}: sem acertos nem nota.";
                $ignoradas++;
                continue;
            }

            $acertos = $acertosRaw !== '' ? (int) $this->num($acertosRaw) : 0;
            $nota    = $notaRaw !== '' ? $this->num($notaRaw) : (float) $acertos;

            $existe = Nota::where('inscricao_id', $insc->id)
                ->where('etapa_id', $etapa->id)
                ->where('disciplina', $disciplina)
                ->exists();

            Nota::updateOrCreate(
                ['inscricao_id' => $insc->id, 'etapa_id' => $etapa->id, 'disciplina' => $disciplina],
                [
                    'tenant_id'    => $etapa->tenant_id,
                    'acertos'      => $acertos,
                    'nota_bruta'   => $nota,
                    'nota_final'   => $nota,
                    'calculada_em' => now(),
                ],
            );

            $existe ? $atualizadas++ : $criadas++;
        }

        return compact('criadas', 'atualizadas', 'ignoradas', 'erros', 'linhas');
    }

    private function num(string $v): float
    {
        $v = str_replace([' ', "\u{00A0}"], '', $v);
        // decimal com virgula: "18,50" -> "18.50" ; remove separador de milhar
        if (str_contains($v, ',')) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        }
        return (float) $v;
    }

    private function normNumero(string $v): string
    {
        $v = trim($v);
        // "0001" e "1" devem casar: remove zeros a esquerda mas guarda o literal tambem
        return ltrim($v, '0') !== '' ? ltrim($v, '0') : ($v !== '' ? '0' : '');
    }

    /** @param array<int,string> $header @param array<int,string> $nomes */
    private function findCol(array $header, array $nomes): ?int
    {
        foreach ($nomes as $n) {
            $k = array_search($n, $header, true);
            if ($k !== false) {
                return $k;
            }
        }
        return null;
    }

    private function slug(string $s): string
    {
        $s = strtolower(trim($s));
        $s = preg_replace('/^\xEF\xBB\xBF/', '', $s);
        $s = strtr($s, [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
            'é' => 'e', 'ê' => 'e', 'í' => 'i',
            'ó' => 'o', 'õ' => 'o', 'ô' => 'o', 'ú' => 'u', 'ç' => 'c',
        ]);
        return preg_replace('/[^a-z0-9]+/', '_', $s);
    }
}
