<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use App\Models\Resultado;
use Illuminate\Http\Request;

class ResultadoController extends Controller
{
    public function form() { return view('portal.resultado', ['resultados' => null, 'cpf' => null]); }

    public function consultar(Request $request)
    {
        $data = $request->validate(['cpf' => 'required|string']);
        $cpf = preg_replace('/\D/', '', $data['cpf']);
        $candidato = Candidato::where('cpf', $cpf)->first();

        $resultados = collect();
        if ($candidato) {
            $ids = $candidato->inscricoes()->pluck('id');
            $resultados = Resultado::whereIn('inscricao_id', $ids)
                ->with(['etapa.concurso', 'inscricao'])->get();
        }
        return view('portal.resultado', ['resultados' => $resultados, 'cpf' => $data['cpf']]);
    }
}
