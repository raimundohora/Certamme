<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    public function store(Request $request, Inscricao $inscricao)
    {
        abort_unless($inscricao->candidato_id === Auth::guard('candidato')->id(), 403);
        $data = $request->validate([
            'tipo' => 'required|string',
            'arquivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);
        $path = $request->file('arquivo')->store('documentos', 'public');
        $inscricao->documentos()->create([
            'tenant_id' => $inscricao->tenant_id,
            'tipo' => $data['tipo'],
            'arquivo' => $path,
            'status' => 'pendente',
        ]);
        return back()->with('ok', 'Documento enviado! Aguarde a análise.');
    }
}
