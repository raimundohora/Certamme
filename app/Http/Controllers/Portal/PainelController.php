<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PainelController extends Controller
{
    public function index()
    {
        $candidato = Auth::guard('candidato')->user();

        $inscricoes = $candidato->inscricoes()
            ->with([
                'concurso',
                'cargo',
                'resultados.etapa:id,ordem,concurso_id',
                'alocacoes.sala.local',
                'alocacoes.etapa:id,ordem',
                'convocacoes' => fn ($q) => $q->where('publicado', true),
                'convocacoes.etapa:id,ordem',
            ])
            ->latest()
            ->get();

        return view('portal.painel', compact('candidato', 'inscricoes'));
    }
}
