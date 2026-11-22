<?php
namespace App\Mail;

use App\Models\Heteroidentificacao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HeteroidentificacaoAnalisada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Heteroidentificacao $het) {}

    public function envelope(): Envelope
    {
        $concurso = $this->het->inscricao->concurso->nome ?? 'concurso';
        $res = $this->het->status?->value === 'deferida' ? 'deferida' : 'indeferida';
        return new Envelope(subject: 'Heteroidentificação ' . $res . ' — ' . $concurso);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.heteroidentificacao-analisada');
    }
}
