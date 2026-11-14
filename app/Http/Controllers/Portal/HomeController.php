<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Concurso;

class HomeController extends Controller
{
    public function index()
    {
        $concursos = Concurso::orderByDesc('ano')->orderBy('nome')->get();
        return view('portal.home', compact('concursos'));
    }
}
