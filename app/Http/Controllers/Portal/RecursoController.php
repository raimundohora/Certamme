<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecursoController extends Controller
{
    public function store(Request $request, Inscricao $inscricao)
    {
        abort_unless($inscricao->candidato_id === Auth::guard('candidato')->id(), 403);
        $data = $request->validate([
            'tipo' => 'required|string',
            'questao' => 'nullable|integer',
            'texto' => 'required|string|max:5000',
        ]);
        $inscricao->recursos()->create([
            'tenant_id' => $inscricao->tenant_id,
            'tipo' => $data['tipo'],
            'questao' => $data['questao'] ?? null,
            'texto' => $data['texto'],
            'status' => 'pendente',
        ]);
        return back()->with('ok', 'Recurso enviado! Acompanhe a resposta aqui.');
    }
}
