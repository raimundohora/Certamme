<?php
namespace App\Http\Controllers\Portal;

use App\Enums\MetodoPagamento;
use App\Enums\Modalidade;
use App\Enums\StatusInscricao;
use App\Http\Controllers\Controller;
use App\Models\Concurso;
use App\Models\Inscricao;
use App\Models\Pagamento;
use App\Services\PagamentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscricaoController extends Controller
{
    public function create(Concurso $concurso)
    {
        $candidato = Auth::guard('candidato')->user();
        if (! $candidato->perfilCompleto()) {
            return redirect()->route('portal.perfil')->with('info', 'Complete seu cadastro antes de se inscrever.');
        }
        $concurso->load('cargos');
        return view('portal.inscrever', compact('concurso'));
    }

    public function store(Request $request, Concurso $concurso)
    {
        $data = $request->validate([
            'cargo_id' => 'required|exists:cargos,id',
            'cidade_prova' => 'required|string|max:255',
            'modalidade' => 'required',
            'condicao_especial' => 'nullable|string|max:255',
        ]);
        $candidato = Auth::guard('candidato')->user();

        $existe = Inscricao::where('concurso_id', $concurso->id)->where('candidato_id', $candidato->id)->first();
        if ($existe) {
            return redirect()->route('portal.comprovante', $existe)->with('info', 'Você já está inscrito neste concurso.');
        }

        $numero = str_pad((string) (Inscricao::where('concurso_id', $concurso->id)->count() + 1), 5, '0', STR_PAD_LEFT);
        $inscricao = Inscricao::create([
            'concurso_id' => $concurso->id,
            'candidato_id' => $candidato->id,
            'cargo_id' => $data['cargo_id'],
            'numero_inscricao' => $numero,
            'nome' => $candidato->nome,
            'modalidade' => Modalidade::from($data['modalidade']),
            'status' => StatusInscricao::PENDENTE,
        ]);

        app(PagamentoService::class)->criar($inscricao, 80.00, MetodoPagamento::PIX);

        return redirect()->route('portal.comprovante', $inscricao)->with('ok', 'Inscrição realizada! Finalize o pagamento.');
    }

    public function comprovante(Inscricao $inscricao)
    {
        abort_unless($inscricao->candidato_id === Auth::guard('candidato')->id(), 403);
        $inscricao->load(["concurso", "cargo", "documentos", "isencao", "recursos", "convocacoes" => fn($q) => $q->where("publicado", true)->with("etapa")]);
        $pagamento = Pagamento::where('inscricao_id', $inscricao->id)->first();
        return view('portal.comprovante', compact('inscricao', 'pagamento'));
    }
}
