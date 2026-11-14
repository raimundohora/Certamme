<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Pagamento;
use App\Models\Resultado;

class RelatorioController extends Controller
{
    public function classificados(Etapa $etapa)
    {
        $resultados = Resultado::where('etapa_id', $etapa->id)
            ->with('inscricao')
            ->orderByRaw('classificacao IS NULL, classificacao')
            ->get();

        $linhas = [['Classificacao', 'Inscricao', 'Nome', 'Modalidade', 'Total', 'Acertos', 'Situacao']];
        foreach ($resultados as $r) {
            $linhas[] = [
                $r->classificacao ?? '-',
                $r->inscricao->numero_inscricao,
                $r->inscricao->nome,
                $r->inscricao->modalidade?->value ?? '',
                number_format((float) $r->total, 2, ',', '.'),
                $r->acertos,
                $r->eliminado ? 'Eliminado' : 'Classificado',
            ];
        }
        return $this->csv($linhas, "classificados-etapa-{$etapa->id}.csv");
    }

    public function arrecadacao(Concurso $concurso)
    {
        $pagos = Pagamento::whereHas('inscricao', fn ($q) => $q->where('concurso_id', $concurso->id))
            ->where('status', 'pago')->get();
        $total = $pagos->sum(fn ($p) => (float) $p->valor);

        $linhas = [
            ['Concurso', 'Pagamentos pagos', 'Arrecadacao (R$)'],
            [$concurso->nome, $pagos->count(), number_format($total, 2, ',', '.')],
        ];
        return $this->csv($linhas, "arrecadacao-concurso-{$concurso->id}.csv");
    }

    private function csv(array $linhas, string $nome)
    {
        $out = fopen('php://temp', 'r+');
        foreach ($linhas as $l) {
            fputcsv($out, $l, ';');
        }
        rewind($out);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($out); // BOM UTF-8 p/ Excel
        fclose($out);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nome}\"",
        ]);
    }
}
