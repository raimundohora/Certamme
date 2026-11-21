<?php
namespace App\Observers;

use App\Mail\ConvocacaoPublicada;
use App\Models\Convocacao;
use Illuminate\Support\Facades\Mail;

class ConvocacaoObserver
{
    public function updated(Convocacao $convocacao): void
    {
        // dispara apenas quando passou a publicado = true
        if (! ($convocacao->wasChanged('publicado') && $convocacao->publicado)) {
            return;
        }
        $email = $convocacao->inscricao?->candidato?->email;
        if (! $email) {
            return;
        }
        try {
            Mail::to($email)->send(new ConvocacaoPublicada($convocacao));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
