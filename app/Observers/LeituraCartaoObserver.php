<?php
namespace App\Observers;

use App\Jobs\CalcularNotaProva;
use App\Models\LeituraCartao;

class LeituraCartaoObserver
{
    /** Gabarito-exclusivo: ao consertar a leitura de um candidato, recalcula a nota da prova. */
    public function updated(LeituraCartao $leitura): void
    {
        if ($leitura->wasChanged('respostas')) {
            CalcularNotaProva::dispatchSync($leitura->prova_id, $leitura->tenant_id);
        }
    }
}
