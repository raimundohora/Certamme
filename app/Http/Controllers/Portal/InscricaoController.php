<?php
namespace App\Http\Controllers\Portal;

use App\Enums\MetodoPagamento;
use App\Enums\Modalidade;
use App\Enums\StatusInscricao;
use App\Http\Controllers\Controller;
use App\Mail\InscricaoRealizada;
use App\Models\Cargo;
use App\Models\Concurso;
use App\Models\Inscricao;
use App\Models\Pagamento;
use App\Services\PagamentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class InscricaoController extends Controller
{
    public function create(Concurso $concurso)
    {
        $candidato = Auth::guard('candidato')->user();
        if (! $candidato->perfilCompleto()) {
            return redirect()->route('portal.perfil')->with('info', 'Complete seu cadastro antes de se inscrever.');
        }
        $concurso->load(['cargos', 'camposInscricao']);
        return view('portal.inscrever', compact('concurso'));
    }

    public function store(Request $request, Concurso $concurso)
    {
        $concurso->load('camposInscricao');
        $campos = $concurso->camposInscricao->where('visivel', true);

        $rules = [
            'cargo_id' => 'required|exists:cargos,id',
            'cidade_prova' => 'required|string|max:255',
            'modalidade' => 'required',
            'metodo' => 'required|in:pix,boleto',
            'condicao_especial' => 'nullable|string|max:255',
        ];
        $attributes = [];
        foreach ($campos as $c) {
            $key = 'campo_' . $c->id;
            $attributes[$key] = $c->rotulo;
            $r = [$c->obrigatorio ? 'required' : 'nullable'];
            switch ($c->tipo->value) {
                case 'numero':   $r[] = 'numeric'; break;
                case 'data':     $r[] = 'date'; break;
                case 'checkbox': $r[] = 'boolean'; break;
                case 'select':   $r[] = 'string'; if ($c->opcoes) { $r[] = Rule::in($c->opcoes); } break;
                default:         $r[] = 'string'; $r[] = 'max:1000';
            }
            $rules[$key] = $r;
        }

        $data = $request->validate($rules, [], $attributes);
        $candidato = Auth::guard('candidato')->user();

        $existe = Inscricao::where('concurso_id', $concurso->id)->where('candidato_id', $candidato->id)->first();
        if ($existe) {
            return redirect()->route('portal.comprovante', $existe)->with('info', 'Você já está inscrito neste concurso.');
        }

        $cargo = Cargo::findOrFail($data['cargo_id']);
        $taxa = (float) ($cargo->taxa_inscricao ?? 0);

        // Monta as respostas dos campos dinâmicos (snapshot rótulo+valor)
        $extras = [];
        foreach ($campos as $c) {
            $key = 'campo_' . $c->id;
            $valor = $c->tipo->value === 'checkbox' ? $request->boolean($key) : ($data[$key] ?? null);
            $extras[$c->chave] = ['rotulo' => $c->rotulo, 'tipo' => $c->tipo->value, 'valor' => $valor];
        }

        $numero = str_pad((string) (Inscricao::where('concurso_id', $concurso->id)->count() + 1), 5, '0', STR_PAD_LEFT);
        $inscricao = Inscricao::create([
            'concurso_id' => $concurso->id,
            'candidato_id' => $candidato->id,
            'cargo_id' => $data['cargo_id'],
            'numero_inscricao' => $numero,
            'nome' => $candidato->nome,
            'modalidade' => Modalidade::from($data['modalidade']),
            'status' => $taxa > 0 ? StatusInscricao::PENDENTE : StatusInscricao::CONFIRMADA,
            'campos_extras' => $extras ?: null,
        ]);

        if ($taxa > 0) {
            $pagamento = app(PagamentoService::class)->criar($inscricao, $taxa, MetodoPagamento::from($data['metodo']));
            try {
                app(PagamentoService::class)->criarNoGateway($pagamento);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($candidato->email) {
            try {
                Mail::to($candidato->email)->send(new InscricaoRealizada($inscricao->load(['concurso', 'cargo'])));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $msg = $taxa > 0 ? 'Inscrição realizada! Finalize o pagamento.' : 'Inscrição gratuita confirmada!';
        return redirect()->route('portal.comprovante', $inscricao)->with('ok', $msg);
    }

    public function comprovante(Inscricao $inscricao)
    {
        abort_unless($inscricao->candidato_id === Auth::guard('candidato')->id(), 403);
        $inscricao->load(['concurso', 'cargo', 'documentos', 'isencao', 'recursos', 'convocacoes' => fn ($q) => $q->where('publicado', true)->with('etapa')]);
        $pagamento = Pagamento::where('inscricao_id', $inscricao->id)->first();
        if ($pagamento && ! $pagamento->external_id) {
            try {
                app(PagamentoService::class)->criarNoGateway($pagamento);
                $pagamento->refresh();
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return view('portal.comprovante', compact('inscricao', 'pagamento'));
    }
}
