<?php
namespace App\Services;

use App\Models\Nota;

class OverrideNotaService
{
    /** Ajuste manual da nota (ex.: recurso deferido). Fica "travado" contra recalculo e e auditado. */
    public function aplicar(Nota $nota, float $novaNota, string $motivo, ?string $autor = null): Nota
    {
        $nota->update([
            'override'        => true,
            'nota_override'   => $novaNota,
            'nota_final'      => $novaNota,
            'override_motivo' => $motivo,
            'override_por'    => $autor,
        ]);

        return $nota->refresh();
    }
}
