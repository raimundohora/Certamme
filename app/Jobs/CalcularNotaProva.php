<?php
namespace App\Jobs;

use App\Models\Prova;
use App\Models\Tenant;
use App\Services\CalcularNotaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalcularNotaProva implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $provaId, public ?int $tenantId = null) {}

    public function handle(CalcularNotaService $svc): void
    {
        if ($this->tenantId) {
            Tenant::setCurrent(Tenant::find($this->tenantId));
        }
        $prova = Prova::find($this->provaId);
        if ($prova) {
            $svc->calcular($prova);
        }
    }
}
