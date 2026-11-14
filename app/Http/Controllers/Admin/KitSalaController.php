<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alocacao;
use App\Models\Etapa;
use Barryvdh\DomPDF\Facade\Pdf;

class KitSalaController extends Controller
{
    public function listaPresenca(Etapa $etapa)
    {
        $grupos = Alocacao::where('etapa_id', $etapa->id)
            ->with(['inscricao', 'sala.local'])
            ->get()
            ->groupBy('sala_id');
        $etapa->loadMissing('concurso');

        $pdf = Pdf::loadView('pdf.lista-presenca', compact('etapa', 'grupos'));
        return $pdf->stream("lista-presenca-etapa-{$etapa->id}.pdf");
    }
}
