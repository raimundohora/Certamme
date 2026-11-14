<?php
namespace App\Observers;

use App\Jobs\CalcularNotaProva;
use App\Models\GabaritoItem;

class GabaritoItemObserver
{
    /** Ao mudar situacao/resposta de uma questao ja existente, reprocessa as notas da prova. */
    public function updated(GabaritoItem $item): void
    {
        if ($item->wasChanged(['situacao', 'resposta_correta', 'resposta_nova'])) {
            CalcularNotaProva::dispatchSync($item->prova_id, $item->tenant_id);
        }
    }
}
