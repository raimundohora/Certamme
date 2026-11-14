<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsencaoController extends Controller
{
    public function store(Request $request, Inscricao $inscricao)
    {
        abort_unless($inscricao->candidato_id === Auth::guard('candidato')->id(), 403);
        $data = $request->validate([
            'nis' => 'nullable|string|max:20',
            'justificativa' => 'nullable|string|max:500',
        ]);
        $inscricao->isencao()->updateOrCreate(
            ['inscricao_id' => $inscricao->id],
            [
                'tenant_id' => $inscricao->tenant_id,
                'nis' => $data['nis'] ?? null,
                'justificativa' => $data['justificativa'] ?? null,
                'status' => 'pendente',
            ],
        );
        return back()->with('ok', 'Pedido de isenção enviado! Aguarde a análise.');
    }
}
