<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Concurso;
use App\Models\Etapa;

class ConcursoController extends Controller
{
    public function show(Concurso $concurso)
    {
        $concurso->load([
            'cargos',
            'anexos' => fn ($q) => $q->where('publicado', true)->latest(),
            'cronogramas' => fn ($q) => $q->orderBy('data'),
        ]);

        $temClassificacao = Etapa::where('concurso_id', $concurso->id)
            ->where('resultado_publicado', true)
            ->exists();

        return view('portal.concurso', compact('concurso', 'temClassificacao'));
    }
}
