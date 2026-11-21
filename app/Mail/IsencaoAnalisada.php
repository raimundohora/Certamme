<?php
namespace App\Mail;

use App\Models\Isencao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IsencaoAnalisada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Isencao $isencao) {}

    public function envelope(): Envelope
    {
        $concurso = $this->isencao->inscricao->concurso->nome ?? 'concurso';
        $res = $this->isencao->status?->value === 'deferido' ? 'deferido' : 'indeferido';
        return new Envelope(subject: 'Pedido de isenção ' . $res . ' — ' . $concurso);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.isencao-analisada');
    }
}
