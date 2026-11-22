<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etapa;
use App\Services\KitSalaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KitSalaController extends Controller
{
    public function listaPresenca(Etapa $etapa)
    {
        $dados = app(KitSalaService::class)->dadosListaPresenca($etapa);
        $pdf = Pdf::loadView('pdf.lista-presenca', $dados)->setPaper('a4');

        return $pdf->stream("lista-presenca-etapa-{$etapa->ordem}.pdf");
    }

    public function mapaSala(Etapa $etapa)
    {
        $dados = app(KitSalaService::class)->dadosMapaSala($etapa);
        $pdf = Pdf::loadView('pdf.mapa-sala', $dados)->setPaper('a4', 'landscape');

        return $pdf->stream("mapa-sala-etapa-{$etapa->ordem}.pdf");
    }

    public function ata(Etapa $etapa)
    {
        $dados = app(KitSalaService::class)->dadosAta($etapa);
        $pdf = Pdf::loadView('pdf.ata-sala', $dados)->setPaper('a4');

        return $pdf->stream("ata-sala-etapa-{$etapa->ordem}.pdf");
    }

    public function cartoes(Etapa $etapa, Request $request)
    {
        $qtd = $request->integer('questoes') ?: null;
        $alt = $request->integer('alternativas') ?: 5;

        $dados = app(KitSalaService::class)->dadosCartoes($etapa, $qtd, $alt);
        $pdf = Pdf::loadView('pdf.cartoes-resposta', $dados)->setPaper('a4');

        return $pdf->stream("cartoes-resposta-etapa-{$etapa->ordem}.pdf");
    }
}
