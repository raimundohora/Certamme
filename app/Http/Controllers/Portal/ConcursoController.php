<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Concurso;

class ConcursoController extends Controller
{
    public function show(Concurso $concurso)
    {
        $concurso->load("cargos");
        return view('portal.concurso', compact('concurso'));
    }
}
