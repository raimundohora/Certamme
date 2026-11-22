<?php

namespace App\Http\Controllers\Portal;

use App\Enums\TipoCertificado;
use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use App\Services\CertificadoService;
use Illuminate\Support\Facades\Auth;

class CertificadoController extends Controller
{
    /** Candidato emite/baixa um documento oficial da própria inscrição. */
    public function emitir(Inscricao $inscricao, string $tipo, CertificadoService $svc)
    {
        abort_unless($inscricao->candidato_id === Auth::guard('candidato')->id(), 403);

        $t = TipoCertificado::tryFrom($tipo);
        abort_unless($t !== null, 404);
        abort_unless($svc->podeEmitir($inscricao, $t), 403, 'Documento indisponível para esta inscrição.');

        $cert = $svc->emitir($inscricao, $t);

        return $svc->pdf($cert)->stream($t->arquivo($inscricao->numero_inscricao));
    }

    /** Página pública de conferência de autenticidade. */
    public function verificar(CertificadoService $svc, ?string $codigo = null)
    {
        $codigo = $codigo !== null ? trim($codigo) : '';
        $cert   = $codigo !== '' ? $svc->verificar($codigo) : null;

        return view('portal.autenticar', ['cert' => $cert, 'codigo' => $codigo]);
    }
}
