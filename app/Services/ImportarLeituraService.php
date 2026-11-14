<?php
namespace App\Services;

use App\Enums\OrigemLeitura;
use App\Models\Importacao;
use App\Models\Inscricao;
use App\Models\LeituraCartao;
use App\Models\Prova;

class ImportarLeituraService
{
    /**
     * CSV: 1 linha por candidato. Col 1 = numero_inscricao; colunas seguintes = respostas em ordem.
     * Ex.: "1001,A,B,C,D,E"  -> respostas {"1":"A","2":"B",...}
     * Idempotente: 1 leitura por (prova, inscricao) via updateOrCreate.
     */
    public function import(Prova $prova, string $csv, OrigemLeitura $origem = OrigemLeitura::CSV): Importacao
    {
        $prova->loadMissing('etapa');
        $concursoId = $prova->etapa->concurso_id;

        $lines = preg_split('/\r\n|\r|\n/', trim($csv));
        $total = 0; $importados = 0; $erros = [];

        $importacao = Importacao::create(['prova_id' => $prova->id, 'arquivo' => null]);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $cols = array_map('trim', explode(',', $line));
            $numero = $cols[0] ?? '';
            $low = strtolower($numero);
            if ($numero === '' || $low === 'inscricao' || $low === 'numero_inscricao') {
                continue; // cabecalho / linha vazia
            }
            $total++;

            $inscricao = Inscricao::where('concurso_id', $concursoId)
                ->where('numero_inscricao', $numero)->first();

            if (! $inscricao) {
                $erros[] = ['linha' => $line, 'motivo' => "inscricao {$numero} nao encontrada"];
                continue;
            }

            $respostas = [];
            foreach (array_slice($cols, 1) as $i => $resp) {
                $respostas[(string) ($i + 1)] = strtoupper($resp);
            }

            LeituraCartao::updateOrCreate(
                ['prova_id' => $prova->id, 'inscricao_id' => $inscricao->id],
                ['respostas' => $respostas, 'origem' => $origem, 'importacao_id' => $importacao->id],
            );
            $importados++;
        }

        $importacao->update([
            'total' => $total, 'importados' => $importados,
            'erros' => count($erros), 'erros_detalhe' => $erros,
        ]);

        return $importacao->refresh();
    }
}
