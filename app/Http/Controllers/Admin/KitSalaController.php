<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etapa;
use App\Services\KitSalaService;
use Barryvdh\DomPDF\Facade\Pdf;

class KitSalaController extends Controller
{
    public function listaPresenca(Etapa $etapa)
    {
        $dados = app(KitSalaService::class)->dadosListaPresenca($etapa);
        $pdf = Pdf::loadView('pdf.lista-presenca', $dados)->setPaper('a4');

        return $pdf->stream("lista-presenca-etapa-{$etapa->ordem}.pdf");
    }
}
