<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Resultado;
use Illuminate\Http\Request;

/**
 * Classificação pública por concurso: mostra o ranking das etapas cuja classificação
 * foi PUBLICADA pelo admin (etapas.resultado_publicado). Sem publicação, nada aparece.
 */
class ClassificacaoController extends Controller
{
    public function concurso(Concurso $concurso, Request $request)
    {
        $etapas = Etapa::where('concurso_id', $concurso->id)
            ->where('resultado_publicado', true)
            ->orderBy('ordem')
            ->get();

        $etapa  = null;
        $linhas = collect();

        if ($etapas->isNotEmpty()) {
            $sel   = $request->integer('etapa');
            $etapa = $etapas->firstWhere('ordem', $sel) ?? $etapas->last();

            $linhas = Resultado::where('etapa_id', $etapa->id)
                ->with([
                    'inscricao:id,numero_inscricao,nome,cargo_id,modalidade',
                    'inscricao.cargo:id,nome',
                ])
                ->orderByRaw('classificacao is null, classificacao')
                ->get();
        }

        return view('portal.classificacao', compact('concurso', 'etapas', 'etapa', 'linhas'));
    }
}
