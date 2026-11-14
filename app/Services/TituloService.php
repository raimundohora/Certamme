<?php
namespace App\Services;

use App\Enums\StatusTitulo;
use App\Models\Titulo;

class TituloService
{
    /** Banca avalia: pontos limitados ao teto do tipo; auditado. */
    public function avaliar(Titulo $titulo, float $pontos, string $avaliador): Titulo
    {
        $titulo->loadMissing('tipo');
        $teto = (float) $titulo->tipo->pontuacao_maxima;
        $titulo->update([
            'pontos'    => min($pontos, $teto),
            'status'    => StatusTitulo::AVALIADO,
            'avaliador' => $avaliador,
        ]);

        return $titulo->refresh();
    }

    /** Conferencia (duplo-check): marca como conferido. */
    public function conferir(Titulo $titulo, string $conferente): Titulo
    {
        $titulo->update([
            'status'     => StatusTitulo::CONFERIDO,
            'conferente' => $conferente,
        ]);

        return $titulo->refresh();
    }
}
