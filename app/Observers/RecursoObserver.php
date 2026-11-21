<?php
namespace App\Observers;

use App\Enums\StatusRecurso;
use App\Mail\RecursoRespondido;
use App\Models\Recurso;
use Illuminate\Support\Facades\Mail;

class RecursoObserver
{
    public function updated(Recurso $recurso): void
    {
        $decidido = in_array($recurso->status, [StatusRecurso::DEFERIDO, StatusRecurso::INDEFERIDO], true);
        if (! ($recurso->wasChanged('status') && $decidido)) {
            return;
        }
        $email = $recurso->inscricao?->candidato?->email;
        if (! $email) {
            return;
        }
        try {
            Mail::to($email)->send(new RecursoRespondido($recurso));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
