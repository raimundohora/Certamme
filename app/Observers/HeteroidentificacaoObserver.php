<?php
namespace App\Observers;

use App\Enums\StatusHeteroidentificacao;
use App\Mail\HeteroidentificacaoAnalisada;
use App\Models\Heteroidentificacao;
use Illuminate\Support\Facades\Mail;

class HeteroidentificacaoObserver
{
    public function updated(Heteroidentificacao $het): void
    {
        if (! $het->wasChanged('status')) {
            return;
        }
        if (! in_array($het->status, [StatusHeteroidentificacao::DEFERIDA, StatusHeteroidentificacao::INDEFERIDA], true)) {
            return;
        }
        $email = $het->inscricao?->candidato?->email;
        if (! $email) {
            return;
        }
        try {
            Mail::to($email)->send(new HeteroidentificacaoAnalisada($het));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
