<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Concurso;

class HomeController extends Controller
{
    public function index()
    {
        // No domínio-base puro (apex), com a vitrine ligada, mostra o site do produto.
        if (config('certamme.landing_on_apex') && request()->attributes->get('certamme_apex')) {
            return view('portal.landing');
        }

        $concursos = Concurso::orderByDesc('ano')->orderBy('nome')->get();
        return view('portal.home', compact('concursos'));
    }
}
