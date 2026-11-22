<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Resultado;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Classificação pública por concurso: mostra o ranking das etapas cuja classificação
 * foi PUBLICADA pelo admin (etapas.resultado_publicado). Sem publicação, nada aparece.
 * Suporta busca por nome (?q=) e download do PDF oficial.
 */
class ClassificacaoController extends Controller
{
    public function concurso(Concurso $concurso, Request $request)
    {
        $etapas = $this->etapasPublicadas($concurso);
        $etapa  = null;
        $linhas = collect();
        $q      = trim((string) $request->query('q', ''));

        if ($etapas->isNotEmpty()) {
            $etapa  = $this->etapaSelecionada($etapas, $request->integer('etapa'));
            $linhas = $this->ranking($etapa, $q);
        }

        return view('portal.classificacao', compact('concurso', 'etapas', 'etapa', 'linhas', 'q'));
    }

    public function pdf(Concurso $concurso, Request $request)
    {
        $etapas = $this->etapasPublicadas($concurso);
        abort_if($etapas->isEmpty(), 404, 'Classificação não publicada.');

        $etapa  = $this->etapaSelecionada($etapas, $request->integer('etapa'));
        $linhas = $this->ranking($etapa, '');
        $orgao  = Tenant::current()?->nome ?? 'Órgão realizador';

        $pdf = Pdf::loadView('pdf.classificacao', compact('concurso', 'etapa', 'linhas', 'orgao'))
            ->setPaper('a4');

        return $pdf->stream("classificacao-{$concurso->id}-etapa-{$etapa->ordem}.pdf");
    }

    /** @return \Illuminate\Support\Collection<int,Etapa> */
    private function etapasPublicadas(Concurso $concurso)
    {
        return Etapa::where('concurso_id', $concurso->id)
            ->where('resultado_publicado', true)
            ->orderBy('ordem')
            ->get();
    }

    private function etapaSelecionada($etapas, ?int $ordem): Etapa
    {
        return $etapas->firstWhere('ordem', $ordem) ?? $etapas->last();
    }

    private function ranking(Etapa $etapa, string $q)
    {
        return Resultado::where('etapa_id', $etapa->id)
            ->when($q !== '', fn ($query) => $query->whereHas(
                'inscricao',
                fn ($i) => $i->where(fn ($w) => $w->where('nome', 'ilike', '%' . $q . '%')
                    ->orWhere('numero_inscricao', 'ilike', '%' . $q . '%'))
            ))
            ->with([
                'inscricao:id,numero_inscricao,nome,cargo_id,modalidade',
                'inscricao.cargo:id,nome',
            ])
            ->orderByRaw('classificacao is null, classificacao')
            ->get();
    }
}
