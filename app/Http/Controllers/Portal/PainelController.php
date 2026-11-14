<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PainelController extends Controller
{
    public function index()
    {
        $candidato = Auth::guard('candidato')->user();
        $inscricoes = $candidato->inscricoes()->with(['concurso', 'cargo'])->latest()->get();
        return view('portal.painel', compact('candidato', 'inscricoes'));
    }
}
