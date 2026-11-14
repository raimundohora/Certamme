<?php
namespace App\Services;

use App\Enums\StatusSubjetiva;
use App\Models\CorrecaoSubjetiva;
use App\Models\ProvaSubjetiva;

class SubjetivaService
{
    public function adicionarCorrecao(ProvaSubjetiva $ps, string $corretor, float $nota, ?array $quesitos = null): CorrecaoSubjetiva
    {
        return $ps->correcoes()->create([
            'tenant_id' => $ps->tenant_id,
            'corretor'  => $corretor,
            'nota'      => $nota,
            'quesitos'  => $quesitos,
        ]);
    }

    /**
     * Consolida a nota da prova subjetiva:
     *  - < 2 correcoes: pendente.
     *  - 2 correcoes: se divergirem alem do limite -> DIVERGENTE (chama 3o); senao media das 2.
     *  - >= 3: resolve pela media das 2 notas mais proximas (pratica padrao quando vai p/ banca).
     */
    public function consolidar(ProvaSubjetiva $ps, float $limiteDivergencia = 2.0): ProvaSubjetiva
    {
        $notas = $ps->correcoes()->orderBy('id')->pluck('nota')->map(fn ($n) => (float) $n)->values()->all();
        $n = count($notas);

        if ($n < 2) {
            $ps->update(['status' => StatusSubjetiva::PENDENTE, 'nota_final' => null]);
            return $ps->refresh();
        }

        if ($n === 2) {
            if (abs($notas[0] - $notas[1]) > $limiteDivergencia) {
                $ps->update(['status' => StatusSubjetiva::DIVERGENTE, 'nota_final' => null]);
                return $ps->refresh();
            }
            $ps->update(['status' => StatusSubjetiva::CORRIGIDA, 'nota_final' => round(($notas[0] + $notas[1]) / 2, 2)]);
            return $ps->refresh();
        }

        // n >= 3: media das 2 mais proximas
        sort($notas);
        $par = [$notas[0], $notas[1]];
        $menorDif = abs($notas[0] - $notas[1]);
        for ($i = 1; $i < $n - 1; $i++) {
            $d = abs($notas[$i] - $notas[$i + 1]);
            if ($d < $menorDif) {
                $menorDif = $d;
                $par = [$notas[$i], $notas[$i + 1]];
            }
        }
        $ps->update(['status' => StatusSubjetiva::CORRIGIDA, 'nota_final' => round(array_sum($par) / 2, 2)]);
        return $ps->refresh();
    }
}
