<?php
namespace App\Observers;

use App\Enums\StatusIsencao;
use App\Mail\IsencaoAnalisada;
use App\Models\Isencao;
use Illuminate\Support\Facades\Mail;

class IsencaoObserver
{
    public function updated(Isencao $isencao): void
    {
        if (! $isencao->wasChanged('status')) {
            return;
        }
        if (! in_array($isencao->status, [StatusIsencao::DEFERIDO, StatusIsencao::INDEFERIDO], true)) {
            return;
        }
        $email = $isencao->inscricao?->candidato?->email;
        if (! $email) {
            return;
        }
        try {
            Mail::to($email)->send(new IsencaoAnalisada($isencao));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
