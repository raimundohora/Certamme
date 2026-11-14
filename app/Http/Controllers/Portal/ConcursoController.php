<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Concurso;

class ConcursoController extends Controller
{
    public function show(Concurso $concurso)
    {
        $concurso->load([
            'cargos',
            'anexos' => fn ($q) => $q->where('publicado', true)->latest(),
            'cronogramas' => fn ($q) => $q->orderBy('data'),
        ]);
        return view('portal.concurso', compact('concurso'));
    }
}
